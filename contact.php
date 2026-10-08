<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

$page_title = 'Contact Us';
$page_description = 'Write to the FoodFusion kitchen about recipes, workshops, and school visits.';
$current_page = 'contact';

$errors = [];
$old = [
    'name' => '',
    'email' => '',
    'subject' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name'] = trim((string) ($_POST['name'] ?? ''));
    $old['email'] = trim((string) ($_POST['email'] ?? ''));
    $old['subject'] = trim((string) ($_POST['subject'] ?? ''));
    $old['message'] = trim((string) ($_POST['message'] ?? ''));

    if (!ff_csrf_ok($_POST['csrf'] ?? null)) {
        $errors['form'] = 'The form expired. Refresh the page and try again.';
    }
    if (!ff_valid_name($old['name']) || strlen($old['name']) > 120) {
        $errors['name'] = 'Please enter your name.';
    }
    if (!ff_valid_email($old['email'])) {
        $errors['email'] = 'Enter a valid email address.';
    }
    if (strlen($old['subject']) < 3 || strlen($old['subject']) > 160) {
        $errors['subject'] = 'Add a subject of at least 3 characters.';
    }
    if (strlen($old['message']) < 10 || strlen($old['message']) > 2000) {
        $errors['message'] = 'Write a message of at least 10 characters.';
    }

    if ($errors === []) {
        if (empty($_SESSION['ff_messages']) || !is_array($_SESSION['ff_messages'])) {
            $_SESSION['ff_messages'] = [];
        }
        $_SESSION['ff_messages'][] = [
            'name' => $old['name'],
            'email' => $old['email'],
            'subject' => $old['subject'],
            'message' => $old['message'],
            'submitted_at' => date('Y-m-d H:i:s'),
        ];
        $_SESSION['ff_last_contact_name'] = $old['name'];
        ff_redirect('contact.php?sent=1');
    }
}

$sent = isset($_GET['sent']);
$sentName = (string) ($_SESSION['ff_last_contact_name'] ?? '');

require __DIR__ . '/includes/header.php';
?>
<main id="main">
    <header class="page-hero">
        <div class="container">
            <p class="eyebrow">Contact us</p>
            <h1>Write to the kitchen.</h1>
            <p class="lede">Recipe questions, a seat at a lab, or a school visit. We reply to the email address you enter.</p>
        </div>
    </header>
    <section class="section-space pt-3">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-7">
                    <?php if ($sent): ?>
                        <div class="success-panel" role="status">
                            <p class="eyebrow">Received</p>
                            <h2>Message received<?= $sentName !== '' ? ', ' . e($sentName) : '' ?>.</h2>
                            <p class="mb-0">Thank you. We have your message and will reply during kitchen hours.</p>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($errors['form'])): ?>
                        <div class="alert alert-danger" role="alert"><?= e($errors['form']) ?></div>
                    <?php endif; ?>
                    <form method="post" action="contact.php" id="contactForm" novalidate>
                        <input type="hidden" name="csrf" value="<?= e(ff_csrf_token()) ?>">
                        <div class="mb-3">
                            <label class="form-label" for="contactName">Name</label>
                            <input class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" id="contactName" name="name" maxlength="120" required value="<?= e($old['name']) ?>" autocomplete="name">
                            <div class="invalid-feedback"><?= e($errors['name'] ?? 'Please enter your name.') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contactEmail">Email</label>
                            <input class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" id="contactEmail" name="email" type="email" maxlength="190" required value="<?= e($old['email']) ?>" autocomplete="email">
                            <div class="invalid-feedback"><?= e($errors['email'] ?? 'Enter a valid email address.') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contactSubject">Subject</label>
                            <input class="form-control<?= isset($errors['subject']) ? ' is-invalid' : '' ?>" id="contactSubject" name="subject" maxlength="160" required value="<?= e($old['subject']) ?>">
                            <div class="invalid-feedback"><?= e($errors['subject'] ?? 'Add a subject of at least 3 characters.') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contactMessage">Message</label>
                            <textarea class="form-control<?= isset($errors['message']) ? ' is-invalid' : '' ?>" id="contactMessage" name="message" rows="6" maxlength="2000" required><?= e($old['message']) ?></textarea>
                            <div class="invalid-feedback"><?= e($errors['message'] ?? 'Write a message of at least 10 characters.') ?></div>
                        </div>
                        <button class="btn btn-ff" type="submit">Send message</button>
                    </form>
                </div>
                <div class="col-lg-5">
                    <aside class="studio-card">
                        <p class="eyebrow">Studio</p>
                        <h2>FoodFusion test kitchen</h2>
                        <p>Workshops run in hired community halls. Use this form to ask for a seat, or to write about a recipe.</p>
                        <p>Email <a href="mailto:hello@foodfusion.test">hello@foodfusion.test</a><br>Phone 01632 960 482</p>
                        <p class="mb-0">Hours for replies: Tuesday to Sunday, 10:00–18:00.</p>
                    </aside>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
