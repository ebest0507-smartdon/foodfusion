<?php
declare(strict_types=1);

/**
 * Prepared by includes/init.php and includes/data.php before this page runs.
 *
 * @var list<array{title: string, kind: string, detail: string, href: string}> $ff_culinary_downloads
 * @var list<array{id: string, title: string, minutes: int, level: string, summary: string, steps: list<string>}> $ff_tutorials
 */

require_once __DIR__ . '/includes/init.php';

$page_title = 'Culinary Resources';
$page_description = 'Download FoodFusion recipe cards, technique cheat-sheets, kitchen hacks, and open short cooking tutorials.';
$current_page = 'culinary';

require __DIR__ . '/includes/header.php';
?>
<main id="main">
    <header class="page-hero">
        <div class="container">
            <p class="eyebrow">Culinary resources</p>
            <h1>Cards, cuts, and habits for the hob.</h1>
            <p class="lede">Printable recipe cards and cheat-sheets, plus short tutorials you can open on this page. The downloads are single HTML pages you can print.</p>
        </div>
    </header>
    <section class="section-space pt-3">
        <div class="container">
            <h2 class="mb-3">Download and keep</h2>
            <div class="resource-grid">
                <?php foreach ($ff_culinary_downloads as $item): ?>
                    <article class="resource-card">
                        <p class="eyebrow"><?= e((string) $item['kind']) ?></p>
                        <h3><?= e((string) $item['title']) ?></h3>
                        <p><?= e((string) $item['detail']) ?></p>
                        <a class="btn btn-ff btn-sm" href="<?= e((string) $item['href']) ?>" download>Download</a>
                    </article>
                <?php endforeach; ?>
            </div>

            <h2 class="mt-5 mb-3">Video-style tutorials</h2>
            <p class="lede">These are step outlines rather than hosted video files. Open one and cook along.</p>
            <div class="resource-grid mt-3">
                <?php foreach ($ff_tutorials as $tutorial): ?>
                    <article class="resource-card">
                        <button class="play-card" type="button" data-tutorial-open="<?= e((string) $tutorial['id']) ?>">
                            <p class="eyebrow"><?= e((string) $tutorial['level']) ?> · <?= (int) $tutorial['minutes'] ?> min</p>
                            <h3><?= e((string) $tutorial['title']) ?></h3>
                            <p><?= e((string) $tutorial['summary']) ?></p>
                            <span class="btn btn-ff-olive btn-sm">Play outline</span>
                        </button>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
