<?php
/**
 * index.php
 * Main entry point — bootstraps the app and renders the library view.
 * Delegates display logic to read.php and the add form to create.php.
 */

session_start();
require_once 'classes/Book.php';
require_once 'classes/BookLibrary.php';

$library = new BookLibrary();

// Consume flash message
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>The Shelf — Book Library</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Mono:wght@400;500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ── Header ─────────────────────────────────────────────── -->
<header>
  <div class="wrap">
    <div class="header-inner">
      <div>
        <div class="logo">The <span>Shelf</span></div>
        <div class="tagline">// personal book library · php oop crud</div>
      </div>
    </div>
  </div>
</header>

<div class="wrap">
  <div class="main-grid">

    <!-- ── Left: Book list (READ) ──────────────────────────── -->
    <div>
      <?php if ($flash): ?>
        <div class="flash <?= $flash['type'] === 'success' ? 'flash-ok' : 'flash-err' ?>">
          <?= h($flash['msg']) ?>
        </div>
      <?php endif; ?>

      <?php require 'read.php'; ?>
    </div>

    <!-- ── Right: Add Book Form (CREATE) ───────────────────── -->
    <div class="form-panel">
      <h2>Add New Book</h2>

      <form method="POST" action="create.php">

        <div class="form-group">
          <label for="title">Title</label>
          <input type="text" id="title" name="title" required placeholder="Book title">
        </div>

        <div class="form-group">
          <label for="author">Author</label>
          <input type="text" id="author" name="author" required placeholder="Author name">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" placeholder="e.g. Fiction">
          </div>
          <div class="form-group">
            <label for="year">Year</label>
            <input type="number" id="year" name="year"
                   min="1000" max="<?= date('Y') ?>"
                   placeholder="<?= date('Y') ?>">
          </div>
        </div>

        <div class="form-group">
          <label for="status">Status</label>
          <select id="status" name="status">
            <option value="unread">Unread</option>
            <option value="reading">Currently Reading</option>
            <option value="read">Finished</option>
          </select>
        </div>

        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Add Book</button>
        </div>

      </form>
    </div>

  </div><!-- /.main-grid -->
</div><!-- /.wrap -->

</body>
</html>
