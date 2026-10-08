<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

$page_title = 'Register';
$page_description = 'Create a FoodFusion account and join the kitchen.';
$current_page = 'register';

$errors = [];
$old = ['first_name' => '', 'last_name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['logout'])) {
    $old['first_name'] = trim((string) ($_POST['first_name'] ?? ''));
    $old['last_name'] = trim((string) ($_POST['last_name'] ?? ''));
    $old['email'] = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    if (!ff_csrf_ok($_POST['csrf'] ?? null)) {
        $errors['form'] = 'The form expired. Refresh the page and try again.';
    }
    if (!ff_valid_name($old['first_name'])) {
        $errors['first_name'] = 'Enter your first name.';
    }
    if (!ff_valid_name($old['last_name'])) {
        $errors['last_name'] = 'Enter your last name.';
    }
    if (!ff_valid_email($old['email'])) {
        $errors['email'] = 'Enter a valid email address.';
    } elseif (ff_find_user($old['email'])) {
        $errors['email'] = 'That email is already registered.';
    }
    if (!ff_valid_password($password)) {
        $errors['password'] = 'Use at least 8 characters with a letter and a number.';
    }

    if ($errors === []) {
        if (empty($_SESSION['ff_registered']) || !is_array($_SESSION['ff_registered'])) {
            $_SESSION['ff_registered'] = [];
        }
        $_SESSION['ff_registered'][] = [
            'id' => 100 + count($_SESSION['ff_registered']),
            'first_name' => $old['first_name'],
            'last_name' => $old['last_name'],
            'email' => $old['email'],
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'failed_attempts' => 0,
            'lockout_time' => null,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        ff_flash('success', 'Account created. You can log in now.');
        ff_redirect('login.php');
    }
}

require __DIR__ . '/includes/header.php';
?>
<main id="main">
    <header class="page-hero">
        <div class="container">
            <p class="eyebrow">Register</p>
            <h1>Join the cookbook.</h1>
            <p class="lede">Choose a password you have not used elsewhere. Your account is ready to sign in as soon as you create it.</p>
        </div>
    </header>
    <section class="section-space pt-3">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-7">
                    <?php if (isset($errors['form'])): ?>
                        <div class="alert alert-danger" role="alert"><?= e($errors['form']) ?></div>
                    <?php endif; ?>
                    <div id="registerErrors" class="alert alert-danger" role="alert" hidden></div>
                    <form method="post" action="register.php" id="registerForm" novalidate>
                        <input type="hidden" name="csrf" value="<?= e(ff_csrf_token()) ?>">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="firstName">First name</label>
                                <input class="form-control<?= isset($errors['first_name']) ? ' is-invalid' : '' ?>" id="firstName" name="first_name" maxlength="80" required value="<?= e($old['first_name']) ?>" autocomplete="given-name">
                                <?php if (isset($errors['first_name'])): ?><div class="invalid-feedback"><?= e($errors['first_name']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="lastName">Last name</label>
                                <input class="form-control<?= isset($errors['last_name']) ? ' is-invalid' : '' ?>" id="lastName" name="last_name" maxlength="80" required value="<?= e($old['last_name']) ?>" autocomplete="family-name">
                                <?php if (isset($errors['last_name'])): ?><div class="invalid-feedback"><?= e($errors['last_name']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" id="email" name="email" type="email" maxlength="190" required value="<?= e($old['email']) ?>" autocomplete="email">
                                <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="password">Password</label>
                                <input class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>" id="password" name="password" type="password" minlength="8" maxlength="72" required autocomplete="new-password">
                                <p class="form-help">At least 8 characters, with a letter and a number.</p>
                                <?php if (isset($errors['password'])): ?><div class="text-danger"><?= e($errors['password']) ?></div><?php endif; ?>
                            </div>
                        </div>
                        <button class="btn btn-ff mt-3" type="submit">Create account</button>
                    </form>
                </div>
                <div class="col-lg-5">
                    <aside class="studio-card">
                        <p class="eyebrow">Already cooking?</p>
                        <h2>Use an existing account</h2>
                        <p>Sample kitchens are listed on the sign-in page. An account you create here is ready to use straight away.</p>
                        <a class="btn btn-ff-ghost" href="login.php">Go to log in</a>
                    </aside>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
