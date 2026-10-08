<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

$page_title = 'Recipe Collection';
$page_description = 'Filter FoodFusion recipes by cuisine, dietary preference, and difficulty, then open the full method.';
$current_page = 'recipes';

$cuisines = [];
$diets = [];
foreach ($ff_recipes as $recipe) {
    $cuisines[$recipe['cuisine_type']] = true;
    $diets[$recipe['dietary_pref']] = true;
}
$cuisines = array_keys($cuisines);
$diets = array_keys($diets);
sort($cuisines);
sort($diets);
$difficulties = ['Easy', 'Medium', 'Hard'];

require __DIR__ . '/includes/header.php';
?>
<main id="main">
    <header class="page-hero">
        <div class="container">
            <p class="eyebrow">Recipe collection</p>
            <h1>Recipes worth repeating.</h1>
            <p class="lede">Filter by cuisine, diet, and difficulty. Open a card for the ingredient list and the method. You can download the open recipe as a plain-text card.</p>
        </div>
    </header>
    <section class="section-space pt-3">
        <div class="container">
            <form id="recipeFilters" class="filter-bar" role="search">
                <div>
                    <label class="form-label" for="filterCuisine">Cuisine type</label>
                    <select class="form-select" id="filterCuisine">
                        <option value="">All cuisines</option>
                        <?php foreach ($cuisines as $cuisine): ?>
                            <option value="<?= e($cuisine) ?>"><?= e($cuisine) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="filterDiet">Dietary preference</label>
                    <select class="form-select" id="filterDiet">
                        <option value="">All diets</option>
                        <?php foreach ($diets as $diet): ?>
                            <option value="<?= e($diet) ?>"><?= e($diet) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="filterDifficulty">Difficulty</label>
                    <select class="form-select" id="filterDifficulty">
                        <option value="">All levels</option>
                        <?php foreach ($difficulties as $level): ?>
                            <option value="<?= e($level) ?>"><?= e($level) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <button class="btn btn-ff-ghost w-100" id="filterReset" type="button">Clear filters</button>
                </div>
            </form>
            <p class="recipe-meta mb-3"><span id="recipeCount"><?= count($ff_recipes) ?></span> recipes shown</p>
            <div class="recipe-grid" id="recipeGrid">
                <?php foreach ($ff_recipes as $recipe): ?>
                    <?php require __DIR__ . '/includes/recipe-card.php'; ?>
                <?php endforeach; ?>
            </div>
            <p id="recipeEmpty" class="empty-state" hidden>No recipes match those filters. Clear them and look again.</p>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
