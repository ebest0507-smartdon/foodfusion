<?php
declare(strict_types=1);

/**
 * Prepared by includes/init.php and includes/data.php before this page runs.
 *
 * @var list<array{number: string, title: string, text: string}> $ff_values
 * @var list<array{name: string, role: string, bio: string, image: string, focus?: string}> $ff_team
 */

require_once __DIR__ . '/includes/init.php';

$page_title = 'About Us';
$page_description = 'FoodFusion’s philosophy, values, and the cooks who test the recipes and edit the community cookbook.';
$current_page = 'about';

require __DIR__ . '/includes/header.php';
?>
<main id="main">
    <header class="page-hero">
        <div class="container">
            <p class="eyebrow">About us</p>
            <h1>A kitchen that trades methods, not slogans.</h1>
            <p class="lede">FoodFusion started as a shared notebook between three cooks who were tired of recipes that hid the useful step. The site is that notebook, opened to anyone who will write clearly.</p>
        </div>
    </header>

    <section class="section-space pt-4">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="about-photo">
                        <img src="images/about-us.jpg" alt="FoodFusion cook in the kitchen with a spatula and whisk">
                    </div>
                </div>
                <div class="col-lg-6">
                    <p class="eyebrow">Philosophy</p>
                    <h2>Recipes are conversations between places.</h2>
                    <p>Jollof rice can sit beside banku and groundnut soup on the same table. The point is not novelty. It is a pot you recognise, ingredients you can buy, and a note another cook can follow on a Wednesday.</p>
                    <p>We name the source of a technique when we know it. “Fusion” here means the cook is honest about what they borrowed and what they changed. Ghanaian food on this site is named clearly: jollof is jollof, red-red is beans and plantain.</p>
                    <p class="mb-0">The learning kitchen is part of the same idea. A lid, a heavy pan, and a decision to turn the hob off early belong in the method, next to the salt.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space pt-0">
        <div class="container">
            <p class="eyebrow">Values</p>
            <h2 class="mb-4">What we will and will not publish</h2>
            <div class="value-grid">
                <?php foreach ($ff_values as $value): ?>
                    <article class="value-card">
                        <p class="value-number"><?= e((string) $value['number']) ?></p>
                        <h3><?= e((string) $value['title']) ?></h3>
                        <p class="mb-0"><?= e((string) $value['text']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section-space pt-0">
        <div class="container">
            <p class="eyebrow">Team</p>
            <h2 class="mb-4">The people testing the pots</h2>
            <div class="team-grid">
                <?php foreach ($ff_team as $member): ?>
                    <article class="team-card">
                        <div class="team-photo">
                            <img src="<?= e((string) $member['image']) ?>" alt="Portrait of <?= e((string) $member['name']) ?>" style="object-position: <?= e((string) ($member['focus'] ?? 'center 20%')) ?>" width="800" height="800" loading="lazy">
                        </div>
                        <h3><?= e((string) $member['name']) ?></h3>
                        <p class="eyebrow"><?= e((string) $member['role']) ?></p>
                        <p><?= e((string) $member['bio']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
