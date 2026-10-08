<?php
declare(strict_types=1);

/**
 * Expects $recipe from the calling loop. Shared by the homepage and the collection.
 *
 * @var array{
 *     id: int|string,
 *     title: string,
 *     description: string,
 *     cuisine_type: string,
 *     dietary_pref: string,
 *     difficulty: string,
 *     image_url: string,
 *     prep_minutes: int,
 *     servings: int,
 *     author: string
 * } $recipe
 */
?>
<article
    class="recipe-card"
    data-recipe-card
    data-id="<?= (int) $recipe['id'] ?>"
    data-cuisine="<?= e((string) $recipe['cuisine_type']) ?>"
    data-diet="<?= e((string) $recipe['dietary_pref']) ?>"
    data-difficulty="<?= e((string) $recipe['difficulty']) ?>"
>
    <img src="<?= e((string) $recipe['image_url']) ?>" alt="<?= e((string) $recipe['title']) ?>" loading="lazy">
    <div class="recipe-card-body">
        <div class="chip-row">
            <span class="chip"><?= e((string) $recipe['cuisine_type']) ?></span>
            <span class="chip"><?= e((string) $recipe['dietary_pref']) ?></span>
            <span class="chip chip-<?= e(strtolower((string) $recipe['difficulty'])) ?>"><?= e((string) $recipe['difficulty']) ?></span>
        </div>
        <h2 class="recipe-title"><?= e((string) $recipe['title']) ?></h2>
        <p class="clamp-3"><?= e((string) $recipe['description']) ?></p>
        <p class="recipe-meta"><?= (int) $recipe['prep_minutes'] ?> min · <?= (int) $recipe['servings'] ?> servings · <?= e((string) $recipe['author']) ?></p>
        <button type="button" class="btn btn-ff btn-sm mt-auto" data-recipe-open="<?= (int) $recipe['id'] ?>">
            View recipe
        </button>
    </div>
</article>
