<?php
declare(strict_types=1);

/**
 * Prepared by includes/init.php and includes/data.php before this page runs.
 *
 * @var list<array<string, mixed>> $ff_recipes
 * @var list<array<string, mixed>> $ff_posts
 * @var list<array{eyebrow: string, title: string, text: string, image: string}> $ff_trends
 * @var list<array{title: string, date: string, place: string, text: string, image: string}> $ff_events
 * @var list<array{name: string, href: string, icon: string}> $ff_socials
 */

require_once __DIR__ . '/includes/init.php';

$page_title = 'Home';
$page_description = 'FoodFusion helps home cooks share recipes across traditions, with clear methods and lower-energy kitchen habits.';
$current_page = 'home';

$cuisineNames = [];
foreach ($ff_recipes as $recipe) {
    $cuisineNames[$recipe['cuisine_type']] = true;
}

require __DIR__ . '/includes/header.php';
?>
<main id="main">
    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <p class="eyebrow">The community kitchen</p>
                    <h1>Cook across cultures. <em>Share the method.</em></h1>
                    <p class="lede">Our mission is to help home cooks share recipes across traditions, with methods clear enough to repeat and habits that waste less heat, food, and fuel.</p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a class="btn btn-ff" href="recipes.php">Browse recipes</a>
                        <button class="btn btn-ff-ghost" type="button" data-bs-toggle="modal" data-bs-target="#signupModal">Join the kitchen</button>
                    </div>
                    <dl class="hero-stats">
                        <div>
                            <dt><?= count($ff_recipes) ?></dt>
                            <dd>tested recipes</dd>
                        </div>
                        <div>
                            <dt><?= count($cuisineNames) ?></dt>
                            <dd>cuisine styles</dd>
                        </div>
                        <div>
                            <dt><?= count($ff_posts) ?></dt>
                            <dd>community notes</dd>
                        </div>
                    </dl>
                </div>
                <div class="col-lg-6">
                    <div class="hero-frame">
                        <img src="images/hero-chef-jollof.jpg" alt="Chef plating jollof rice in the FoodFusion kitchen">
                        <div class="hero-note">
                            <p class="eyebrow">On the stove</p>
                            <p>Jollof rice</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space pt-0">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <p class="eyebrow">How the kitchen works</p>
                    <h2>Three steps, then you are cooking.</h2>
                </div>
            </div>
            <ol class="steps">
                <li><strong>Create an account</strong> Register with your name and email. Your password stays private, and three unsuccessful sign-ins pause the account for three minutes.</li>
                <li><strong>Cook from the collection</strong> Filter by cuisine, diet, and difficulty, then open a recipe card with the full method.</li>
                <li><strong>Leave a note</strong> Add a technique, a swap, or a storage tip to the community cookbook.</li>
            </ol>
        </div>
    </section>

    <section class="section-space pt-0">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end gap-3 mb-4">
                <div>
                    <p class="eyebrow">Featured</p>
                    <h2>From the test kitchen</h2>
                </div>
                <a href="recipes.php">All recipes</a>
            </div>
            <div class="recipe-grid">
                <?php foreach ($ff_recipes as $recipe): ?>
                    <?php if (!empty($recipe['featured'])): ?>
                        <?php require __DIR__ . '/includes/recipe-card.php'; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section-space pt-0">
        <div class="container">
            <p class="eyebrow">News feed</p>
            <h2 class="mb-4">What the kitchen is talking about</h2>
            <div id="trendCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($ff_trends as $index => $trend): ?>
                        <button type="button" data-bs-target="#trendCarousel" data-bs-slide-to="<?= $index ?>"<?= $index === 0 ? ' class="active" aria-current="true"' : '' ?> aria-label="Show trend <?= $index + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-inner">
                    <?php foreach ($ff_trends as $index => $trend): ?>
                        <div class="carousel-item<?= $index === 0 ? ' active' : '' ?>">
                            <article class="trend-slide">
                                <img src="<?= e((string) $trend['image']) ?>" alt="">
                                <div class="trend-caption">
                                    <p class="eyebrow"><?= e((string) $trend['eyebrow']) ?></p>
                                    <h2><?= e((string) $trend['title']) ?></h2>
                                    <p><?= e((string) $trend['text']) ?></p>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#trendCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#trendCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <section class="section-space pt-0">
        <div class="container">
            <p class="eyebrow">Calendar</p>
            <h2 class="mb-4">Cooking events</h2>
            <div class="event-grid">
                <?php foreach ($ff_events as $event): ?>
                    <article class="event-card">
                        <img src="<?= e((string) $event['image']) ?>" alt="" loading="lazy">
                        <div class="event-body">
                            <p class="eyebrow"><?= e(ff_format_date((string) $event['date'])) ?> · <?= e((string) $event['place']) ?></p>
                            <h3><?= e((string) $event['title']) ?></h3>
                            <p class="mb-0"><?= e((string) $event['text']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section-space pt-0">
        <div class="container">
            <div class="social-band">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <p class="eyebrow">Social</p>
                        <h2>Follow the kitchen</h2>
                        <p class="mb-0">Recipe tests, event notes, and the occasional badly timed photo of barley. These links open public cooking tags and searches.</p>
                    </div>
                    <div class="col-lg-4">
                        <div class="social-row">
                            <?php foreach ($ff_socials as $social): ?>
                                <a class="social-link" href="<?= e((string) $social['href']) ?>" target="_blank" rel="noopener noreferrer" aria-label="FoodFusion on <?= e((string) $social['name']) ?>">
                                    <i class="bi <?= e((string) $social['icon']) ?>" aria-hidden="true"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="join-band mt-4">
                <div>
                    <p class="eyebrow">Sign up</p>
                    <h2>Bring a recipe from your kitchen.</h2>
                </div>
                <button class="btn btn-ff" type="button" data-bs-toggle="modal" data-bs-target="#signupModal">Join us</button>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
