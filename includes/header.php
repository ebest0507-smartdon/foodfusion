<?php
declare(strict_types=1);

/**
 * Prepared by the including page and includes/init.php before this file is required.
 *
 * @var string|null $page_title
 * @var string|null $page_description
 * @var string|null $current_page
 * @var array<string, mixed>|null $ff_viewer
 */

$page_title = $page_title ?? 'FoodFusion';
$page_description = $page_description ?? 'FoodFusion is a community culinary website for recipes, cooking resources, and lower-energy kitchen practice.';
$current_page = $current_page ?? '';
$ff_viewer = $ff_viewer ?? null;
$ff_nav = [
    'home' => ['index.php', 'Home'],
    'about' => ['about.php', 'About'],
    'recipes' => ['recipes.php', 'Recipes'],
    'community' => ['community.php', 'Community'],
    'culinary' => ['culinary-resources.php', 'Culinary'],
    'learn' => ['edu-resources.php', 'Learning'],
    'contact' => ['contact.php', 'Contact'],
];
$ff_flash_message = ff_consume_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($page_description) ?>">
    <meta name="theme-color" content="#1c1612">
    <title><?= e($page_title) ?> · FoodFusion</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,560;0,9..144,650;1,9..144,560&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
</head>
<body class="page-<?= e($current_page) ?>">
<a class="skip-link" href="#main">Skip to content</a>
<nav class="navbar navbar-expand-xl ff-nav sticky-top">
    <div class="container">
        <a class="navbar-brand ff-brand" href="index.php">
            <?php require __DIR__ . '/logo.php'; ?>
            FoodFusion
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Open menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto mb-2 mb-xl-0">
                <?php foreach ($ff_nav as $key => $item): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $current_page === $key ? ' active' : '' ?>" href="<?= e($item[0]) ?>"<?= $current_page === $key ? ' aria-current="page"' : '' ?>><?= e($item[1]) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="nav-auth">
                <?php if ($ff_viewer): ?>
                    <span class="nav-user">Hello, <?= e((string) $ff_viewer['first_name']) ?></span>
                    <form method="post" action="login.php">
                        <input type="hidden" name="csrf" value="<?= e(ff_csrf_token()) ?>">
                        <input type="hidden" name="logout" value="1">
                        <button class="btn btn-ff-ghost btn-sm" type="submit">Log out</button>
                    </form>
                <?php else: ?>
                    <a class="btn btn-ff-ghost btn-sm" href="login.php">Log in</a>
                    <a class="btn btn-ff btn-sm" href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<?php if ($ff_flash_message): ?>
    <div class="container pt-3">
        <div class="alert alert-<?= e((string) $ff_flash_message['type']) ?> mb-0" role="status"><?= e((string) $ff_flash_message['text']) ?></div>
    </div>
<?php endif; ?>
<noscript>
    <div class="container pt-3">
        <p class="alert alert-warning mb-0">Filters, the live cookbook feed, and the cookie bar use JavaScript. Sign-in, registration, and the contact form still work without it.</p>
    </div>
</noscript>
