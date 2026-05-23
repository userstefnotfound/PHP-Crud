<?php
/**
 * create.php
 * Handles the CREATE operation — adds a new book to the library.
 */

session_start();
require_once 'classes/Book.php';
require_once 'classes/BookLibrary.php';

$library = new BookLibrary();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title  = trim($_POST['title']  ?? '');
    $author = trim($_POST['author'] ?? '');
    $genre  = trim($_POST['genre']  ?? '');
    $year   = (int)($_POST['year']  ?? date('Y'));
    $status = $_POST['status']      ?? 'unread';

    $validStatuses = ['unread', 'reading', 'read'];

    if ($title === '' || $author === '') {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Title and author are required.'];
    } elseif (!in_array($status, $validStatuses)) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Invalid status value.'];
    } else {
        $book = $library->addBook($title, $author, $genre, $year, $status);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => "\"" . $book->title . "\" was added to your library."];
    }
}

header('Location: index.php');
exit;
