<?php
declare(strict_types=1);

/**
 * Prepared by includes/init.php and includes/data.php before this page runs.
 *
 * @var list<array{method: string, reaches: int, detail: string}> $ff_energy
 * @var list<array{title: string, kind: string, detail: string, href: string}> $ff_edu_downloads
 */

require_once __DIR__ . '/includes/init.php';

$page_title = 'Educational Resources';
$page_description = 'Guides on induction, gas, seasonal cooking, composting, and renewable energy in the home kitchen.';
$current_page = 'learn';

require __DIR__ . '/includes/header.php';
?>
<main id="main">
    <header class="page-hero">
        <div class="container">
            <p class="eyebrow">Educational resources</p>
            <h1>Cook with less fuel, and say so honestly.</h1>
            <p class="lede">The learning kitchen covers renewable electricity, induction, lids, seasonal buying, and what belongs in a small compost bin. Ranges are labelled as ranges.</p>
        </div>
    </header>

    <section class="section-space pt-3">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-7">
                    <p class="eyebrow">Infographic</p>
                    <h2>Share of hob energy that reaches the pan</h2>
                    <p>These bars are approximate figures published in appliance comparisons. They are not a meter reading from our kitchen. A microwave is different again: it heats the food directly, so it is often the lowest-energy way to reheat one portion.</p>
                    <?php foreach ($ff_energy as $row): ?>
                        <div class="energy-row">
                            <div class="energy-label">
                                <span><?= e((string) $row['method']) ?></span>
                                <span><?= (int) $row['reaches'] ?>%</span>
                            </div>
                            <div class="energy-track" role="img" aria-label="<?= e((string) $row['method']) ?> about <?= (int) $row['reaches'] ?> percent">
                                <div class="energy-fill" style="--share: <?= (int) $row['reaches'] ?>%"></div>
                            </div>
                            <p class="energy-detail"><?= e((string) $row['detail']) ?></p>
                        </div>
                    <?php endforeach; ?>
                    <details class="mt-4">
                        <summary>What these figures are</summary>
                        <p class="mt-2 mb-0">Induction is often cited around 85–90%, radiant or coil hobs near 70%, and gas hobs near 40%, because a gas flame heats the room as well as the pan. Your hob, pan, and what you are cooking will move the number.</p>
                    </details>
                </div>
                <div class="col-lg-5">
                    <aside class="studio-card">
                        <p class="eyebrow">Renewable energy</p>
                        <h2>Where the electricity comes from</h2>
                        <p>An induction hob uses electricity. On a grid with wind, solar, or hydro, that cook has a lower carbon cost than the same hob on a coal-heavy hour. A renewable tariff does not change the physics of the pan. It changes what generated the kilowatt-hour.</p>
                        <p>A solar cooker uses sunlight directly and no grid fuel while the sky is clear. It is a slow cooker, not a weekday breakfast tool in a cloudy climate.</p>
                        <p class="mb-0">A retained-heat box finishes food that has already boiled, using insulation instead of more fuel. Soups, beans, and rice suit it. The food still has to be cooked through and kept hot, not left lukewarm for hours.</p>
                    </aside>
                </div>
            </div>

            <h2 class="mt-5 mb-3">Guides to download</h2>
            <div class="resource-grid">
                <?php foreach ($ff_edu_downloads as $item): ?>
                    <article class="resource-card">
                        <p class="eyebrow"><?= e((string) $item['kind']) ?></p>
                        <h3><?= e((string) $item['title']) ?></h3>
                        <p><?= e((string) $item['detail']) ?></p>
                        <a class="btn btn-ff btn-sm" href="<?= e((string) $item['href']) ?>" download>Download</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
