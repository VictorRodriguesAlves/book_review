<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use PhpParser\Node\Expr\Array_;

class BookService
{

    public function createBook(array $data): Book
    {
       return Book::query()->create($data);
    }


    public function getAllBooks(): Collection
    {
        return Book::query()->get();
    }
}
