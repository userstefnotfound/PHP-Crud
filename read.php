<?php
/**
 * read.php
 * Handles the READ operation — displays all books, supports search.
 * Included by index.php; not accessed directly.
 */

// $library must already be initialized by index.php

$search = trim($_GET['search'] ?? '');
$books  = $search ? $library->search($search) : $library->getAll();
$stats  = $library->stats();

function statusBadge(string $s): string {
    return match($s) {
        'read'    => '<span class="badge badge-read">Read</span>',
        'reading' => '<span class="badge badge-reading">Reading</span>',
        default   => '<span class="badge badge-unread">Unread</span>',
    };
}
?>

<div class="stats-bar">
    <div class="stat">
        <div class="stat-num"><?= $stats['total'] ?></div>
        <div class="stat-label">total books</div>
    </div>
    <div class="stat">
        <div class="stat-num"><?= $stats['read'] ?></div>
        <div class="stat-label">finished</div>
    </div>
    <div class="stat">
        <div class="stat-num"><?= $stats['reading'] ?></div>
        <div class="stat-label">in progress</div>
    </div>
    <div class="stat">
        <div class="stat-num"><?= $stats['unread'] ?></div>
        <div class="stat-label">on the list</div>
    </div>
</div>

<form method="GET" class="search-row" action="index.php">
    <input class="search-input" type="text" name="search"
           placeholder="Search by title, author, or genre…"
           value="<?= htmlspecialchars($search) ?>">
    <button class="btn btn-primary" type="submit">Search</button>
    <?php if ($search): ?>
        <a class="btn btn-ghost" href="index.php">Clear</a>
    <?php endif; ?>
</form>

<div class="section-title">
    <?= $search ? 'Results for "' . htmlspecialchars($search) . '"' : 'Your Collection' ?>
    &nbsp;(<?= count($books) ?>)
</div>

<?php if (empty($books)): ?>
    <div class="empty">No books found. Add one using the form →</div>
<?php else: ?>
    <div class="book-list">
        <?php foreach ($books as $b): ?>
            <div class="book-card">
                <div>
                    <div class="book-title"><?= htmlspecialchars($b->title) ?></div>
                    <div class="book-meta">
                        <?= htmlspecialchars($b->author) ?>
                        &nbsp;·&nbsp; <?= htmlspecialchars($b->genre) ?>
                        &nbsp;·&nbsp; <?= $b->year ?>
                        &nbsp;&nbsp;<?= statusBadge($b->status) ?>
                    </div>
                </div>
                <div class="book-actions">
                    <!-- Edit button → update.php -->
                    <a class="btn btn-ghost btn-sm"
                       href="update.php?id=<?= $b->id ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                       Edit
                    </a>
                    <!-- Delete form → delete.php -->
                    <form method="POST" action="delete.php" onsubmit="return confirm('Remove this book?')">
                        <input type="hidden" name="id" value="<?= $b->id ?>">
                        <?php if ($search): ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <?php endif; ?>
                        <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
