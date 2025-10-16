<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
uses()->group('auth');

it('should authenticate a user and return a token when valid credentials are provided', function () {

    //arrange
    $user = User::factory()->create([
        'email' => 'john@doe.com',
    ]);

    $data = [
        'email' => $user->email,
        'password' => 'password',
    ];


    //act
    $response = $this->postJson(route('auth.login'), $data);

    //assert
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'user' => [
            'id',
            'name',
            'email',
            'created_at',
            'updated_at',
        ],
        'token'
    ]);


});

it('should return an authentication error when attempting to log in with an incorrect password', function () {
    //arrange
    $user = User::factory()->create([
        'email' => 'john@doe.com',
    ]);

    $data = [
        'email' => $user->email,
        'password' => 'password1',
    ];


    //act
    $response = $this->postJson(route('auth.login'), $data);

    //assert
    $response->assertStatus(401);
    $response->assertJson([
        'message' => 'Invalid credentials.',
    ]);
});

it('should return an authentication error when attempting to log in with an email that does not exist', function () {

    //arrange
    $data = [
        'email' => 'john@doe.com',
        'password' => 'password',
    ];


    //act
    $response = $this->postJson(route('auth.login'), $data);

    //assert
    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
    $response->assertInvalid([
        'email' => 'The selected email is invalid.',
    ]);

});

it('should return a validation error if a field is submitted empty in login', function () {

    //arrange
    $data = [
        'email' => '',
        'password' => '',
    ];

    //act
    $response = $this->postJson(route('auth.login'), $data);

    //assert
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email', 'password']);
});

