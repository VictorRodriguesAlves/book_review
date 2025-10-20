<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->name('auth.register');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('auth.login');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('auth.logout');
});


Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('books')->group(function () {
        Route::get('/', [BookController::class, 'list'])->name('books.list');
        Route::get('/{book}', [BookController::class, 'show'])->name('books.show');
        Route::post('/', [BookController::class, 'store'])->name('books.store');
        Route::post('/{book}/reviews', [ReviewController::class, 'store'])->name('books.reviews.store');

    });

});

