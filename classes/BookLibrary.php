<?php
/**
 * BookLibrary.php
 * Manages the book collection stored in a PHP array (persisted via session).
 */
class BookLibrary {
    private array  $books      = [];
    private int    $nextId     = 1;
    private string $sessionKey = 'book_library';

    public function __construct() {
        if (!isset($_SESSION[$this->sessionKey])) {
            // Seed default books on first load
            $this->addBook('The Midnight Library',  'Matt Haig',         'Fiction',     2020, 'read');
            $this->addBook('Dune',                  'Frank Herbert',     'Sci-Fi',      1965, 'read');
            $this->addBook('Atomic Habits',         'James Clear',       'Self-Help',   2018, 'reading');
            $this->addBook('The Name of the Wind',  'Patrick Rothfuss',  'Fantasy',     2007, 'unread');
            $this->addBook('Sapiens',               'Yuval Noah Harari', 'Non-Fiction', 2011, 'unread');
            $this->save();
        } else {
            $this->load();
        }
    }

    // ── Session persistence ────────────────────────────────────────────────
    private function save(): void {
        $_SESSION[$this->sessionKey] = serialize([
            'books'  => $this->books,
            'nextId' => $this->nextId,
        ]);
    }

    private function load(): void {
        $data         = unserialize($_SESSION[$this->sessionKey]);
        $this->books  = $data['books'];
        $this->nextId = $data['nextId'];
    }

    // ── CREATE ─────────────────────────────────────────────────────────────
    public function addBook(string $title, string $author, string $genre, int $year, string $status = 'unread'): Book {
        $book = new Book($this->nextId++, $title, $author, $genre, $year, $status);
        $this->books[$book->id] = $book;
        $this->save();
        return $book;
    }

    // ── READ ───────────────────────────────────────────────────────────────
    public function getAll(): array {
        return array_values($this->books);
    }

    public function getById(int $id): ?Book {
        return $this->books[$id] ?? null;
    }

    public function search(string $query): array {
        $q = strtolower($query);
        return array_values(array_filter($this->books, fn($b) =>
            str_contains(strtolower($b->title),  $q) ||
            str_contains(strtolower($b->author), $q) ||
            str_contains(strtolower($b->genre),  $q)
        ));
    }

    public function stats(): array {
        $all = $this->getAll();
        return [
            'total'   => count($all),
            'read'    => count(array_filter($all, fn($b) => $b->status === 'read')),
            'reading' => count(array_filter($all, fn($b) => $b->status === 'reading')),
            'unread'  => count(array_filter($all, fn($b) => $b->status === 'unread')),
        ];
    }

    // ── UPDATE ─────────────────────────────────────────────────────────────
    public function updateBook(int $id, string $title, string $author, string $genre, int $year, string $status): bool {
        if (!isset($this->books[$id])) return false;
        $b = $this->books[$id];
        $b->title  = $title;
        $b->author = $author;
        $b->genre  = $genre;
        $b->year   = $year;
        $b->status = $status;
        $this->save();
        return true;
    }

    // ── DELETE ─────────────────────────────────────────────────────────────
    public function deleteBook(int $id): bool {
        if (!isset($this->books[$id])) return false;
        unset($this->books[$id]);
        $this->save();
        return true;
    }
}
