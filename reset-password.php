<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

if (ff_current_user() !== null) {
    ff_redirect('index.php');
}

$page_title = 'Reset password';
$page_description = 'Set a new FoodFusion password for the account you just confirmed.';
$current_page = 'login';

$pending = ff_pending_reset();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['password_confirm'] ?? '');
    $pending = ff_pending_reset();

    if (!ff_csrf_ok($_POST['csrf'] ?? null)) {
        $error = 'The form expired. Refresh the page and try again.';
    } elseif ($pending === null) {
        $error = 'That reset expired. Start again from the email address.';
    } elseif (!ff_valid_password($password)) {
        $error = 'Use at least 8 characters with a letter and a number.';
    } elseif (!hash_equals($password, $confirm)) {
        $error = 'Type the same password in both fields.';
    } elseif (!ff_set_user_password($pending['email'], $password)) {
        $error = 'The password could not be saved. Start again.';
        ff_clear_password_reset();
        $pending = null;
    } else {
        ff_clear_password_reset();
        ff_flash('success', 'Password updated for ' . $pending['email'] . '. Sign in with the new password.');
        ff_redirect('login.php');
    }
}

require __DIR__ . '/includes/header.php';
?>
<main id="main">
    <header class="page-hero">
        <div class="container">
            <p class="eyebrow">Reset password</p>
            <h1>Set the password you will use.</h1>
            <?php if ($pending !== null): ?>
                <p class="lede">This reset is for <strong><?= e($pending['first_name']) ?></strong> (<?= e($pending['email']) ?>). It stays open for 15 minutes.</p>
            <?php else: ?>
                <p class="lede">Start with the email on your account, then choose the password you want to use.</p>
            <?php endif; ?>
        </div>
    </header>
    <section class="section-space pt-3">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
                    <?php endif; ?>
                    <?php if ($pending === null): ?>
                        <div class="success-panel">
                            <h2>No reset is waiting</h2>
                            <p>Enter the email on your account first, then set the password you want to sign in with.</p>
                            <a class="btn btn-ff" href="forgot-password.php">Forgot password</a>
                        </div>
                    <?php else: ?>
                        <div id="resetErrors" class="alert alert-danger" role="alert" hidden></div>
                        <form method="post" action="reset-password.php" id="resetForm" novalidate>
                            <input type="hidden" name="csrf" value="<?= e(ff_csrf_token()) ?>">
                            <div class="mb-3">
                                <label class="form-label" for="password">New password</label>
                                <input class="form-control" id="password" name="password" type="password" minlength="8" maxlength="72" required autocomplete="new-password">
                                <p class="form-help">At least 8 characters, with a letter and a number.</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="passwordConfirm">Confirm password</label>
                                <input class="form-control" id="passwordConfirm" name="password_confirm" type="password" minlength="8" maxlength="72" required autocomplete="new-password">
                            </div>
                            <button class="btn btn-ff" type="submit">Save password</button>
                            <p class="form-help mt-3 mb-0"><a href="forgot-password.php">Use a different email</a></p>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
