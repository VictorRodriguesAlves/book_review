<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Book;

class BookService
{

    public function createBook(array $data): Book
    {
       return Book::query()->create($data);
    }

}
