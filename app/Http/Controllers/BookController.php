<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookService;
use Auth;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{

    public function __construct(private BookService $bookService)
    {
    }


    public function list(): JsonResponse
    {
        $books = $this->bookService->getAllBooks();

        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }

    public function show(Book $book): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new BookResource($book)
        ]);
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $this->bookService->createBook($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Book added successfully',
        ], 201);
    }
    
}
