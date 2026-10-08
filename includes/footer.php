<?php
declare(strict_types=1);

/**
 * Prepared by includes/init.php and includes/data.php before this file is required.
 *
 * @var string|null $ff_viewer_name
 * @var list<array<string, mixed>> $ff_recipes
 * @var list<array<string, mixed>> $ff_tutorials
 * @var list<array{name: string, href: string, icon: string}> $ff_socials
 */

$ff_client = [
    'viewer' => $ff_viewer_name,
    'recipes' => $ff_recipes,
    'tutorials' => $ff_tutorials,
];
$ff_json = json_encode(
    $ff_client,
    JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
if ($ff_json === false) {
    $ff_json = '{}';
}
?>
<footer class="ff-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <p class="ff-brand footer-brand"><?php require __DIR__ . '/logo.php'; ?> FoodFusion</p>
                <p>A community kitchen for recipes you can repeat, notes from other cooks, and habits that waste less heat and food.</p>
            </div>
            <div class="col-6 col-lg-2">
                <h2 class="footer-heading">Explore</h2>
                <ul class="footer-links">
                    <li><a href="recipes.php">Recipes</a></li>
                    <li><a href="community.php">Community cookbook</a></li>
                    <li><a href="culinary-resources.php">Culinary resources</a></li>
                    <li><a href="edu-resources.php">Learning kitchen</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h2 class="footer-heading">Kitchen</h2>
                <ul class="footer-links">
                    <li><a href="about.php">About the cooks</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="login.php">Log in</a></li>
                    <li><button type="button" class="footer-button" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy and cookies</button></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h2 class="footer-heading">Social</h2>
                <div class="social-row">
                    <?php foreach ($ff_socials as $social): ?>
                        <a class="social-link" href="<?= e((string) $social['href']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e((string) $social['name']) ?>">
                            <i class="bi <?= e((string) $social['icon']) ?>" aria-hidden="true"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
                <p class="footer-note">hello@foodfusion.test<br>01632 960 482</p>
            </div>
        </div>
        <p class="footer-copy">© <?= date('Y') ?> FoodFusion. Recipes are for home cooking. Check that meat, grains, and fridge pickles are cooked or stored safely.</p>
    </div>
</footer>
<?php require __DIR__ . '/modals.php'; ?>
<script>window.FF = <?= $ff_json ?>;</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/app.js?v=<?= (int) filemtime(__DIR__ . '/../js/app.js') ?>"></script>
</body>
</html>
