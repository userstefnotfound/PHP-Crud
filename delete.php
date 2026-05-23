<?php
/**
 * delete.php
 * Handles the DELETE operation — removes a book by ID.
 * Accepts POST only; redirects back to index.php after.
 */

session_start();
require_once 'classes/Book.php';
require_once 'classes/BookLibrary.php';

// Block direct GET access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$library = new BookLibrary();
$id      = (int)($_POST['id'] ?? 0);
$search  = $_POST['search'] ?? '';

$book = $library->getById($id);

if ($book && $library->deleteBook($id)) {
    $_SESSION['flash'] = [
        'type' => 'success',
        'msg'  => "\"" . $book->title . "\" was removed from your library.",
    ];
} else {
    $_SESSION['flash'] = [
        'type' => 'error',
        'msg'  => 'Book not found or could not be deleted.',
    ];
}

$redirect = 'index.php';
if ($search !== '') {
    $redirect .= '?search=' . urlencode($search);
}

header('Location: ' . $redirect);
exit;
