<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Book;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    public function __construct(protected ReviewService $reviewService)
    {
    }

    public function store(StoreReviewRequest $request, Book $book): JsonResponse
    {
        $validatedData = $request->validated();

        $this->reviewService->createReview($validatedData, $book);

        return response()->json([
            'success' => true,
            'message' => 'Review added',
        ], 201);
    }

}
