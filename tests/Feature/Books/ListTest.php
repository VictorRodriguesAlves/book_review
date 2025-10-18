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

it('should be possible for a authenticated user to successfully list all books.', function () {

    //arrange
    Book::factory(10)->create();

    //act
    $response = $this->getJson(route('books.list'));

    //assert
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            [
                'id',
                'title',
                'description',
                'average_stars',
                'reviews_count',
                'created_at',
                'updated_at'
            ],
        ]
    ]);


});

it('should return an empty data array when no books exist', function () {
//act
    $response = $this->getJson(route('books.list'));

    //assert
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [

        ]
    ]);
});

it('should return a unauthorized error if an unauthenticated user attempts to list the books', function () {

    //arrange
    Auth::logout();

    //act
    $response = $this->getJson(route('books.list'));

    //assert
    $response->assertUnauthorized();
});


