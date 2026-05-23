<?php
/**
 * update.php
 * Handles the UPDATE operation.
 * GET  → shows the pre-filled edit form.
 * POST → saves the updated book and redirects to index.php.
 */

session_start();
require_once 'classes/Book.php';
require_once 'classes/BookLibrary.php';

$library = new BookLibrary();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['id']    ?? 0);
    $title  = trim($_POST['title']  ?? '');
    $author = trim($_POST['author'] ?? '');
    $genre  = trim($_POST['genre']  ?? '');
    $year   = (int)($_POST['year']  ?? date('Y'));
    $status = $_POST['status']      ?? 'unread';

    $validStatuses = ['unread', 'reading', 'read'];

    if ($title === '' || $author === '') {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Title and author are required.'];
        header('Location: update.php?id=' . $id);
    } elseif (!in_array($status, $validStatuses)) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Invalid status value.'];
        header('Location: update.php?id=' . $id);
    } elseif ($library->updateBook($id, $title, $author, $genre, $year, $status)) {
        $_SESSION['flash'] = ['type' => 'success', 'msg' => "\"$title\" was updated successfully."];
        header('Location: index.php');
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Book not found.'];
        header('Location: index.php');
    }
    exit;
}

$id   = (int)($_GET['id'] ?? 0);
$book = $library->getById($id);

if (!$book) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Book not found.'];
    header('Location: index.php');
    exit;
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Book — The Shelf</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Mono:wght@400;500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
  <div class="wrap">
    <div class="header-inner">
      <div>
        <a href="index.php" class="logo">The <span>Shelf</span></a>
        <div class="tagline">// edit book</div>
      </div>
      <a class="btn btn-ghost" href="index.php">← Back to Library</a>
    </div>
  </div>
</header>

<div class="wrap" style="max-width:560px; padding-top:40px; padding-bottom:60px;">

  <?php
  if (isset($_SESSION['flash'])) {
      $f = $_SESSION['flash'];
      unset($_SESSION['flash']);
      $cls = $f['type'] === 'success' ? 'flash-ok' : 'flash-err';
      echo "<div class=\"flash $cls\">" . h($f['msg']) . "</div>";
  }
  ?>

  <div class="form-panel">
    <h2>Edit Book</h2>

    <form method="POST" action="update.php">
      <input type="hidden" name="id" value="<?= $book->id ?>">

      <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" required
               placeholder="Book title"
               value="<?= h($book->title) ?>">
      </div>

      <div class="form-group">
        <label for="author">Author</label>
        <input type="text" id="author" name="author" required
               placeholder="Author name"
               value="<?= h($book->author) ?>">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="genre">Genre</label>
          <input type="text" id="genre" name="genre"
                 placeholder="e.g. Fiction"
                 value="<?= h($book->genre) ?>">
        </div>
        <div class="form-group">
          <label for="year">Year</label>
          <input type="number" id="year" name="year"
                 min="1000" max="<?= date('Y') ?>"
                 value="<?= $book->year ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status">
          <?php foreach (['unread' => 'Unread', 'reading' => 'Currently Reading', 'read' => 'Finished'] as $val => $label): ?>
            <option value="<?= $val ?>" <?= $book->status === $val ? 'selected' : '' ?>>
              <?= $label ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Save Changes</button>
        <a class="btn btn-ghost" href="index.php">Cancel</a>
      </div>
    </form>
  </div>

</div>
</body>
</html>
