<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
uses()->group('books');

beforeEach(function () {
   $adminUser = User::factory()->create([
       'user_type' => 'admin',
   ]);
   $this->actingAs($adminUser);
});

it('should be possible to successfully add a book if the user is an administrator', function () {

    //arrange
    $data = [
        'title' => 'Test book',
        'description' => 'Test book',
    ];

    //act
    $response = $this->postJson(route('books.store'), $data);

    //assert
    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Book added successfully',
        ]);
    $this->assertDatabaseCount('books', 1);
    $this->assertDatabaseHas('books', [
        'title' => 'Test book',
        'description' => 'Test book',
        'average_stars' => 0,
        'reviews_count' => 0,
    ]);

});

it('should not be possible to add a book if the user is not an administrator', function () {

    //arrange
    $user = User::factory()->create([
        'user_type' => 'user',
    ]);
    $this->actingAs($user);

    $data = [
        'title' => 'Test book',
        'description' => 'Test book',
    ];

    //act
    $response = $this->postJson(route('books.store'), $data);

    //assert
    $response->assertStatus(403);
    $this->assertDatabaseCount('books', 0);
    $this->assertDatabaseMissing('books', [
        'title' => 'Test book',
        'description' => 'Test book',
    ]);
});

it('should return an error when sending empty fields', function () {

    //arrange
    $data = [
        'title' => '',
        'description' => '',
    ];

    //act
    $response = $this->postJson(route('books.store'), $data);

    //assert
    $response->assertStatus(422)
        ->assertInvalid(['title', 'description']);
    $this->assertDatabaseCount('books', 0);
    $this->assertDatabaseMissing('books', [
        'title' => 'Test book',
        'description' => 'Test book',
    ]);
});