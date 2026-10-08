<?php
declare(strict_types=1);

/**
 * Prepared by includes/init.php and includes/data.php before this page runs.
 *
 * @var list<array<string, mixed>> $ff_posts
 * @var list<string> $ff_tip_types
 * @var array<string, mixed>|null $ff_viewer
 * @var string|null $ff_viewer_name
 */

require_once __DIR__ . '/includes/init.php';

$page_title = 'Community Cookbook';
$page_description = 'Share a cooking tip with the FoodFusion community cookbook and read notes from other cooks.';
$current_page = 'community';

$communityError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['community_post'])) {
    $title = trim((string) ($_POST['title'] ?? ''));
    $content = trim((string) ($_POST['content'] ?? ''));
    $tipType = trim((string) ($_POST['tip_type'] ?? ''));
    if (!ff_csrf_ok($_POST['csrf'] ?? null)) {
        $communityError = 'The form expired. Refresh the page and try again.';
    } elseif (strlen($title) < 4 || strlen($title) > 160) {
        $communityError = 'Give the note a title between 4 and 160 characters.';
    } elseif (!in_array($tipType, $ff_tip_types, true)) {
        $communityError = 'Choose a tip type.';
    } elseif (strlen($content) < 20 || strlen($content) > 1000) {
        $communityError = 'Write between 20 and 1000 characters so another cook can use the note.';
    } else {
        if (empty($_SESSION['ff_community']) || !is_array($_SESSION['ff_community'])) {
            $_SESSION['ff_community'] = [];
        }
        array_unshift($_SESSION['ff_community'], [
            'id' => 's-' . bin2hex(random_bytes(4)),
            'user_id' => $ff_viewer !== null ? (int) $ff_viewer['id'] : 0,
            'author' => $ff_viewer_name ?: 'Guest cook',
            'title' => $title,
            'content' => $content,
            'tip_type' => $tipType,
            'likes' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'comments' => [],
        ]);
        ff_redirect('community.php?posted=1');
    }
}

$feed = $ff_posts;
if (!empty($_SESSION['ff_community']) && is_array($_SESSION['ff_community'])) {
    $feed = array_merge($_SESSION['ff_community'], $feed);
}

require __DIR__ . '/includes/header.php';
?>
<main id="main">
    <header class="page-hero">
        <div class="container">
            <p class="eyebrow">Community cookbook</p>
            <h1>Notes from other people’s pans.</h1>
            <p class="lede">Share a technique, a substitution, a storage habit, or a cultural note. Likes and comments appear with the note.</p>
        </div>
    </header>
    <section class="section-space pt-3">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <form method="post" action="community.php" id="communityForm" class="post-card" novalidate>
                        <p class="eyebrow">Contribute</p>
                        <h2>Add a note</h2>
                        <div id="communityError" class="alert alert-danger" role="alert"<?= $communityError === '' ? ' hidden' : '' ?>><?= e($communityError) ?></div>
                        <p id="communityStatus" class="recipe-meta" role="status"><?= isset($_GET['posted']) ? 'Your note is in the feed.' : '' ?></p>
                        <input type="hidden" name="csrf" value="<?= e(ff_csrf_token()) ?>">
                        <input type="hidden" name="community_post" value="1">
                        <div class="mb-3">
                            <label class="form-label" for="postTitle">Title</label>
                            <input class="form-control" id="postTitle" name="title" maxlength="160" required value="<?= e((string) ($_POST['title'] ?? '')) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="postType">Tip type</label>
                            <select class="form-select" id="postType" name="tip_type" required>
                                <option value="">Choose one</option>
                                <?php foreach ($ff_tip_types as $type): ?>
                                    <option value="<?= e($type) ?>"<?= (($_POST['tip_type'] ?? '') === $type) ? ' selected' : '' ?>><?= e($type) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="postContent">Note</label>
                            <textarea class="form-control" id="postContent" name="content" rows="5" maxlength="1000" required><?= e((string) ($_POST['content'] ?? '')) ?></textarea>
                        </div>
                        <button class="btn btn-ff" type="submit">Share with the cookbook</button>
                        <p class="form-help mt-3">Posting as <?= e($ff_viewer_name ?: 'a guest cook') ?>.</p>
                    </form>
                </div>
                <div class="col-lg-8">
                    <div id="communityFeed">
                        <?php foreach ($feed as $post): ?>
                            <article class="post-card" data-post-id="<?= e((string) $post['id']) ?>">
                                <div class="post-card-head">
                                    <div>
                                        <p class="eyebrow"><?= e((string) $post['tip_type']) ?></p>
                                        <h2><?= e((string) $post['title']) ?></h2>
                                    </div>
                                    <p class="post-meta"><?= e((string) $post['author']) ?> · <?= e(ff_format_date((string) $post['created_at'])) ?></p>
                                </div>
                                <p><?= e((string) $post['content']) ?></p>
                                <div class="post-actions">
                                    <button type="button" class="btn btn-ff-ghost btn-sm" data-like aria-pressed="false">
                                        <i class="bi bi-heart" aria-hidden="true"></i>
                                        <span data-like-count><?= (int) ($post['likes'] ?? 0) ?></span>
                                    </button>
                                    <button type="button" class="btn btn-ff-ghost btn-sm" data-comment-toggle aria-expanded="false">
                                        Notes <span data-comment-count><?= count($post['comments'] ?? []) ?></span>
                                    </button>
                                </div>
                                <div class="comment-panel" hidden>
                                    <ul class="comment-list">
                                        <?php foreach (($post['comments'] ?? []) as $comment): ?>
                                            <li><strong><?= e((string) $comment['author']) ?></strong> — <?= e((string) $comment['text']) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <form class="comment-form">
                                        <input class="form-control" maxlength="280" required placeholder="Add a short note" aria-label="Add a short note on <?= e((string) $post['title']) ?>">
                                        <button class="btn btn-ff btn-sm" type="submit">Post</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
