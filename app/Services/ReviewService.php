<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewService
{

    public function createReview(array $data, Book $book): Review
    {
        return $book->reviews()->create($data);
    }


}
