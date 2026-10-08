<?php
declare(strict_types=1);

/**
 * Procedural helpers for FoodFusion.
 * No classes and no framework. Pages call these functions directly.
 * Login lockout is stored in the session so the site can run without MySQL.
 * schema.sql models the same facts in users.failed_attempts and users.lockout_time.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/** bcrypt hash used only to keep password_verify() timing similar when an email is unknown. */
const FF_DUMMY_HASH = '$2y$10$g5JiFEsCqN7xZj/BvOi18uFAxy5g0ujIJ3hq37Z1HVc5tSO1p1cZS';
const FF_LOCKOUT_LIMIT = 3;
const FF_LOCKOUT_SECONDS = 180;
const FF_RESET_SECONDS = 900;

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function ff_redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function ff_flash(string $type, string $text): void
{
    $allowed = ['success', 'danger', 'warning', 'info'];
    if (!in_array($type, $allowed, true)) {
        $type = 'info';
    }
    $_SESSION['ff_flash'] = ['type' => $type, 'text' => $text];
}

function ff_consume_flash(): ?array
{
    if (empty($_SESSION['ff_flash']) || !is_array($_SESSION['ff_flash'])) {
        return null;
    }
    $flash = $_SESSION['ff_flash'];
    unset($_SESSION['ff_flash']);
    return $flash;
}

function ff_csrf_token(): string
{
    if (empty($_SESSION['ff_csrf']) || !is_string($_SESSION['ff_csrf'])) {
        $_SESSION['ff_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['ff_csrf'];
}

function ff_csrf_ok(?string $token): bool
{
    $known = $_SESSION['ff_csrf'] ?? '';
    return is_string($token) && is_string($known) && $known !== '' && hash_equals($known, $token);
}

function ff_valid_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function ff_valid_name(string $name): bool
{
    $name = trim($name);
    $length = function_exists('mb_strlen') ? mb_strlen($name) : strlen($name);
    return $length >= 2 && $length <= 80 && preg_match('/[<>]/', $name) !== 1;
}

function ff_valid_password(string $password): bool
{
    $length = strlen($password);
    if ($length < 8 || $length > 72) {
        return false;
    }
    return preg_match('/[A-Za-z]/', $password) === 1 && preg_match('/\d/', $password) === 1;
}

function ff_format_date(string $date): string
{
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }
    return date('j M Y', $timestamp);
}

function ff_email_key(string $email): string
{
    return strtolower(trim($email));
}

function ff_current_user(): ?array
{
    if (empty($_SESSION['ff_user']) || !is_array($_SESSION['ff_user'])) {
        return null;
    }
    return $_SESSION['ff_user'];
}

function ff_find_user(string $email): ?array
{
    global $ff_users;
    $key = ff_email_key($email);
    $pool = $ff_users;
    if (!empty($_SESSION['ff_registered']) && is_array($_SESSION['ff_registered'])) {
        $pool = array_merge($pool, $_SESSION['ff_registered']);
    }
    $found = null;
    foreach ($pool as $user) {
        if (ff_email_key((string) $user['email']) === $key) {
            $found = $user;
        }
    }
    if ($found === null) {
        return null;
    }
    $override = $_SESSION['ff_password_overrides'][$key] ?? null;
    if (is_string($override) && $override !== '') {
        $found['password'] = $override;
    }
    return $found;
}

/**
 * Stores a new password_hash() for an existing account.
 * Seed accounts keep the new hash in the session. Accounts created
 * in this browser are updated in the session register list as well.
 */
function ff_set_user_password(string $email, string $password): bool
{
    if (!ff_valid_password($password) || ff_find_user($email) === null) {
        return false;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    if (!is_string($hash) || $hash === '') {
        return false;
    }
    $key = ff_email_key($email);
    if (!isset($_SESSION['ff_password_overrides']) || !is_array($_SESSION['ff_password_overrides'])) {
        $_SESSION['ff_password_overrides'] = [];
    }
    $_SESSION['ff_password_overrides'][$key] = $hash;
    if (!empty($_SESSION['ff_registered']) && is_array($_SESSION['ff_registered'])) {
        foreach ($_SESSION['ff_registered'] as $index => $registered) {
            if (ff_email_key((string) ($registered['email'] ?? '')) === $key) {
                $_SESSION['ff_registered'][$index]['password'] = $hash;
            }
        }
    }
    ff_clear_failures($email);
    return true;
}

function ff_begin_password_reset(string $email): bool
{
    $user = ff_find_user($email);
    if ($user === null) {
        return false;
    }
    $_SESSION['ff_reset'] = [
        'email' => ff_email_key((string) $user['email']),
        'expires' => time() + FF_RESET_SECONDS,
    ];
    return true;
}

function ff_pending_reset(): ?array
{
    $reset = $_SESSION['ff_reset'] ?? null;
    if (!is_array($reset) || empty($reset['email']) || empty($reset['expires'])) {
        return null;
    }
    if ((int) $reset['expires'] < time()) {
        unset($_SESSION['ff_reset']);
        return null;
    }
    $user = ff_find_user((string) $reset['email']);
    if ($user === null) {
        unset($_SESSION['ff_reset']);
        return null;
    }
    return [
        'email' => (string) $user['email'],
        'first_name' => (string) $user['first_name'],
        'expires' => (int) $reset['expires'],
    ];
}

function ff_clear_password_reset(): void
{
    unset($_SESSION['ff_reset']);
}

/**
 * Returns the unix time a locked email can try again, or null when it is free.
 * An expired lock is cleared so the attempt counter starts again.
 */
function ff_lockout_until(string $email): ?int
{
    $key = ff_email_key($email);
    if (empty($_SESSION['ff_lockouts'][$key]['until'])) {
        return null;
    }
    $until = (int) $_SESSION['ff_lockouts'][$key]['until'];
    if ($until <= time()) {
        unset($_SESSION['ff_lockouts'][$key]);
        return null;
    }
    return $until;
}

function ff_register_failure(string $email): bool
{
    $key = ff_email_key($email);
    if (empty($_SESSION['ff_lockouts'][$key]) || !is_array($_SESSION['ff_lockouts'][$key])) {
        $_SESSION['ff_lockouts'][$key] = ['attempts' => 0, 'until' => null];
    }
    $_SESSION['ff_lockouts'][$key]['attempts'] = (int) $_SESSION['ff_lockouts'][$key]['attempts'] + 1;
    if ((int) $_SESSION['ff_lockouts'][$key]['attempts'] >= FF_LOCKOUT_LIMIT) {
        $_SESSION['ff_lockouts'][$key]['attempts'] = 0;
        $_SESSION['ff_lockouts'][$key]['until'] = time() + FF_LOCKOUT_SECONDS;
        $_SESSION['ff_last_lock_email'] = $key;
        return true;
    }
    return false;
}

function ff_clear_failures(string $email): void
{
    $key = ff_email_key($email);
    unset($_SESSION['ff_lockouts'][$key]);
    if (($_SESSION['ff_last_lock_email'] ?? '') === $key) {
        unset($_SESSION['ff_last_lock_email']);
    }
}

function ff_attempts_remaining(string $email): int
{
    $key = ff_email_key($email);
    $used = (int) ($_SESSION['ff_lockouts'][$key]['attempts'] ?? 0);
    return max(0, FF_LOCKOUT_LIMIT - $used);
}
