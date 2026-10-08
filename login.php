<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    if (!ff_csrf_ok($_POST['csrf'] ?? null)) {
        ff_flash('danger', 'Log out failed. Refresh the page and try again.');
        ff_redirect('index.php');
    }
    unset($_SESSION['ff_user']);
    ff_flash('success', 'You have logged out.');
    ff_redirect('index.php');
}

$page_title = 'Log in';
$page_description = 'Sign in to your FoodFusion kitchen account.';
$current_page = 'login';

$error = '';
$email = '';
$lockoutUntil = null;
$remaining = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['logout'])) {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!ff_csrf_ok($_POST['csrf'] ?? null)) {
        $error = 'The form expired. Refresh the page and try again.';
    } elseif (!ff_valid_email($email)) {
        $error = 'Enter the email address on the account.';
    } else {
        $lockoutUntil = ff_lockout_until($email);
        if ($lockoutUntil !== null) {
            $error = 'Sign-in for this email is paused. Please wait a few minutes.';
        } else {
            $user = ff_find_user($email);
            if ($user) {
                $valid = password_verify($password, (string) $user['password']);
            } else {
                password_verify($password, FF_DUMMY_HASH);
                $valid = false;
            }

            if (!$valid) {
                $justLocked = ff_register_failure($email);
                if ($justLocked) {
                    $lockoutUntil = ff_lockout_until($email);
                    $error = 'Too many unsuccessful tries. Sign-in is paused for three minutes.';
                } else {
                    $remaining = ff_attempts_remaining($email);
                    $error = 'Email or password is incorrect.';
                }
            } else {
                ff_clear_failures($email);
                session_regenerate_id(true);
                $_SESSION['ff_user'] = [
                    'id' => $user['id'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                ];
                ff_flash('success', 'Welcome back, ' . $user['first_name'] . '.');
                ff_redirect('index.php');
            }
        }
    }
} elseif (!empty($_SESSION['ff_last_lock_email'])) {
    $email = (string) $_SESSION['ff_last_lock_email'];
    $lockoutUntil = ff_lockout_until($email);
}

$locked = $lockoutUntil !== null;

require __DIR__ . '/includes/header.php';
?>
<main id="main">
    <header class="page-hero">
        <div class="container">
            <p class="eyebrow">Log in</p>
            <h1>Back to the kitchen.</h1>
            <p class="lede">Welcome back. After three unsuccessful tries, sign-in for that email pauses for three minutes.</p>
        </div>
    </header>
    <section class="section-space pt-3">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <?php if ($locked): ?>
                        <div class="success-panel lockout-notice" id="lockoutNotice" data-until="<?= (int) $lockoutUntil ?>" role="alert">
                            <h2>Account locked</h2>
                            <p>Please wait <strong id="lockoutClock">3:00</strong>. Sign-in for this email opens again when the timer ends.</p>
                            <p id="lockoutLive" class="mb-0 recipe-meta" aria-live="polite"></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($error !== '' && !$locked): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= e($error) ?>
                            <?php if ($remaining !== null): ?>
                                <?= (int) $remaining ?> attempt<?= $remaining === 1 ? '' : 's' ?> left before sign-in pauses for three minutes.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="login.php" id="loginForm" novalidate>
                        <input type="hidden" name="csrf" value="<?= e(ff_csrf_token()) ?>">
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control" id="email" name="email" type="email" maxlength="190" required autocomplete="username" value="<?= e($email) ?>"<?= $locked ? ' disabled' : '' ?>>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input class="form-control" id="password" name="password" type="password" required autocomplete="current-password"<?= $locked ? ' disabled' : '' ?>>
                        </div>
                        <button class="btn btn-ff" type="submit"<?= $locked ? ' disabled' : '' ?>>Log in</button>
                        <p class="form-help mt-3 mb-0"><a href="forgot-password.php">Forgot password?</a></p>
                        <p class="form-help mt-2">No account yet? <a href="register.php">Register</a> or use the join form on the homepage.</p>
                    </form>
                </div>
                <div class="col-lg-6">
                    <aside class="demo-card">
                        <p class="eyebrow">Sample kitchens</p>
                        <h2>Sign in as a cook</h2>
                        <ul>
                            <li><strong>Amara Okonkwo</strong><br>amara@foodfusion.test<br>Paprika#2026</li>
                            <li class="mt-2"><strong>Leo Martin</strong><br>leo@foodfusion.test<br>Risotto#2026</li>
                            <li class="mt-2"><strong>Priya Shah</strong><br>priya@foodfusion.test<br>Harvest#2026</li>
                        </ul>
                        <p class="mb-0">These accounts are ready to use. Three wrong passwords pause sign-in for three minutes.</p>
                    </aside>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
