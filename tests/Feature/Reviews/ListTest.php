<?php

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
uses()->group('reviews');

beforeEach(function () {
    $this->book = Book::factory()->create();
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('should be possible for a authenticated user to successfully list all reviews of a book.', function () {

    //arrange
    Review::factory()->create([
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
    ]);

    //act
    $response = $this->getJson(route('books.reviews.list', $this->book->id));

    //assert
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            [
                'id',
                'user_id',
                'book_id',
                'stars',
                'body',
                'created_at',
                'updated_at',
            ]
        ]
    ]);

});

it('should return an empty data array when no reviews exist.', function () {


    //act
    $response = $this->getJson(route('books.reviews.list', $this->book->id));

    //assert
    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'data' => []
    ]);

});

it('should return a unauthorized error if an unauthenticated user attempts to list the books.', function () {

    //arrange
    Auth::logout();

    //act
    $response = $this->getJson(route('books.reviews.list', $this->book->id));

    //assert
    $response->assertUnauthorized();



});
