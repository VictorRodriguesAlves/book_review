<?php

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
uses()->group('books');

beforeEach(function () {
    $user = User::factory()->create();
    $this->actingAs($user);
});


it('should be possible to show a book when the user is authenticated', function () {

    //arrange
    $book = Book::factory()->create();

    //act
    $response = $this->getJson(route('books.show', ['book' => $book->id]));

    //assert
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'id',
            'title',
            'description',
            'average_stars',
            'reviews_count',
            'created_at',
            'updated_at',
        ]
    ]);
    $response->assertJson([
        'success' => true,
        'data' => [
            'id' => $book->id,
            'title' => $book->title,
            'description' => $book->description,
            'average_stars' => $book->average_stars,
            'reviews_count' => $book->reviews_count,
        ]
    ]);

});

it('should return a unauthorized error if an unauthenticated user attempts to show a book', function () {

    //arrange
    Auth::logout();
    $book = Book::factory()->create();

    //act
    $response = $this->getJson(route('books.show', ['book' => $book->id]));

    //assert
    $response->assertUnauthorized();

});

it('should return a not found error if the book does not exist', function () {

    //act
    $response = $this->getJson(route('books.show', ['book' => 999]));

    //assert
    $response->assertNotFound();

});