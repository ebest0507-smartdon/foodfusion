<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

if (ff_current_user() !== null) {
    ff_redirect('index.php');
}

$page_title = 'Forgot password';
$page_description = 'Request a new FoodFusion password for an existing account.';
$current_page = 'login';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));

    if (!ff_csrf_ok($_POST['csrf'] ?? null)) {
        $error = 'The form expired. Refresh the page and try again.';
    } elseif (!ff_valid_email($email)) {
        $error = 'Enter the email address on the account.';
    } elseif (!ff_begin_password_reset($email)) {
        $error = 'No account uses that email.';
    } else {
        ff_redirect('reset-password.php');
    }
}

require __DIR__ . '/includes/header.php';
?>
<main id="main">
    <header class="page-hero">
        <div class="container">
            <p class="eyebrow">Forgot password</p>
            <h1>Choose a new password.</h1>
            <p class="lede">Enter the email on your account. You can choose a new password and sign in with it straight away.</p>
        </div>
    </header>
    <section class="section-space pt-3">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
                    <?php endif; ?>
                    <div id="forgotErrors" class="alert alert-danger" role="alert" hidden></div>
                    <form method="post" action="forgot-password.php" id="forgotForm" novalidate>
                        <input type="hidden" name="csrf" value="<?= e(ff_csrf_token()) ?>">
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control" id="email" name="email" type="email" maxlength="190" required autocomplete="username" value="<?= e($email) ?>">
                        </div>
                        <button class="btn btn-ff" type="submit">Continue</button>
                        <p class="form-help mt-3 mb-0"><a href="login.php">Back to log in</a></p>
                    </form>
                </div>
                <div class="col-lg-6">
                    <aside class="demo-card">
                        <p class="eyebrow">Need a hand?</p>
                        <h2>Reset, then sign in</h2>
                        <p>If you still know your password, go back and sign in. Use this page when you have forgotten it, or when sign-in has been paused.</p>
                        <p class="mb-0">Once the new password is saved, you can sign in with it. Any pause on the account is lifted.</p>
                    </aside>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
