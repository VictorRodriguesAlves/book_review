<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Database\Eloquent\Collection;

class ReviewService
{

    public function createBookReview(array $data, Book $book): Review
    {
        return $book->reviews()->create($data);
    }

    public function getReviewsByBook(Book $book): Collection
    {
        return $book->reviews()->get();
    }

}
