<?php

/**
 * Book.php
 * Entity class representing a single book.
 */
class Book {
    public int    $id;
    public string $title;
    public string $author;
    public string $genre;
    public int    $year;
    public string $status; // 'unread' | 'reading' | 'read'

    public function __construct(
        int    $id,
        string $title,
        string $author,
        string $genre,
        int    $year,
        string $status = 'unread'
    ) {
        $this->id     = $id;
        $this->title  = $title;
        $this->author = $author;
        $this->genre  = $genre;
        $this->year   = $year;
        $this->status = $status;
    }
}
