<?php

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
uses()->group('reviews');

beforeEach(function () {
    $this->book = Book::factory()->create();
    $user = User::factory()->create();
    $this->actingAs($user);
});

it('should allow an authenticated user to post a review for a book', function () {

    //arrange
    $data = [
        'body' => 'This is a test review',
        'stars' => 3,
    ];

    //act
    $response = $this->postJson(route('books.reviews.store', ['book' => $this->book->id]), $data);

    //assert
    $response->assertStatus(201);
    $response->assertJsonStructure([
        'success',
        'message',
    ]);
    $this->assertDatabaseHas('reviews', $data);
    $this->assertDatabaseCount('reviews', 1);
});

it('should return a unauthorized error if the user is unauthenticated', function () {

    //arrange
    Auth::logout();
    $data = [
        'body' => 'This is a test review',
        'stars' => 3,
    ];

    //act
    $response = $this->postJson(route('books.reviews.store', ['book' => $this->book->id]), $data);

    //assert
    $response->assertUnauthorized();
    $this->assertDatabaseMissing('reviews', $data);
    $this->assertDatabaseCount('reviews', 0);
});

it('should prevent a user from reviewing the same book twice', function () {
    //arrange
    $data = [
        'body' => 'This is a test review',
        'stars' => 3,
    ];

    //act
    $this->postJson(route('books.reviews.store', ['book' => $this->book->id]), $data);
    $response = $this->postJson(route('books.reviews.store', ['book' => $this->book->id]), $data);

    //assert
    $response->assertStatus(422);
    $response->assertJson([
        'message' => 'You have already reviewed this book.',
    ]);
    $this->assertDatabaseCount('reviews', 1);
});

it('should return a not found error if the book does not exist', function () {

    //arrange
    $data = [
        'body' => '',
        'stars' => '',
    ];

    //act
    $response = $this->postJson(route('books.reviews.store', ['book' => 9999]), $data);

    //assert
    $response->assertNotFound();
    $this->assertDatabaseCount('reviews', 0);

});

it('should return a 422 validation error if some field is missing', function () {

    //arrange
    $data = [
        'body' => '',
        'stars' => '',
    ];

    //act
    $response = $this->postJson(route('books.reviews.store', ['book' => $this->book->id]), $data);

    //assert
    $response->assertStatus(422);
    $response->assertInvalid(['body', 'stars']);
    $this->assertDatabaseCount('reviews', 0);

});

it('should return a 422 validation error if the stars field is not an integer', function () {
    //arrange
    $data = [
        'body' => 'Test',
        'stars' => 'test',
    ];

    //act
    $response = $this->postJson(route('books.reviews.store', ['book' => $this->book->id]), $data);

    //assert
    $response->assertStatus(422);
    $response->assertInvalid(['stars']);
    $this->assertDatabaseCount('reviews', 0);
});

it('should return a 422 validation error if the stars value is less than 1', function () {
    //arrange
    $data = [
        'body' => 'Test',
        'stars' => 0,
    ];

    //act
    $response = $this->postJson(route('books.reviews.store', ['book' => $this->book->id]), $data);

    //assert
    $response->assertStatus(422);
    $response->assertInvalid(['stars']);
    $this->assertDatabaseCount('reviews', 0);
});

it('should return a 422 validation error if the stars value is greater than 5', function () {
    //arrange
    $data = [
        'body' => 'Test',
        'stars' => 6,
    ];

    //act
    $response = $this->postJson(route('books.reviews.store', ['book' => $this->book->id]), $data);

    //assert
    $response->assertStatus(422);
    $response->assertInvalid(['stars']);
    $this->assertDatabaseCount('reviews', 0);
});

it('should return a 422 validation error if the body is too long', function () {
    //arrange
    $data = [
        'body' => Str::random(256),
        'stars' => 5,
    ];

    //act
    $response = $this->postJson(route('books.reviews.store', ['book' => $this->book->id]), $data);

    //assert
    $response->assertStatus(422);
    $response->assertInvalid(['body']);
    $this->assertDatabaseCount('reviews', 0);
});
