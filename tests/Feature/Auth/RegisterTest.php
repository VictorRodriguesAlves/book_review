<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
uses()->group('auth');

it('should register a user successfully when valid data is provided', function () {

        //arrange
        $data = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'password',
        ];


        //act
        $response = $this->postJson(route('auth.register'), $data);

        //assert
        $response->assertStatus(201);
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
        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

    });

it('should return a validation error if the email already exists in register', function () {

        //arrange
        \App\Models\User::factory()->create([
            'email' => 'john@doe.com',
        ]);
        $data = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'password',
        ];


        //act
        $response = $this->postJson(route('auth.register'), $data);

        //assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');


    });

it('should return a validation error if a field is submitted empty in register', function () {

        //arrange
        $data = [
            'name' => '',
            'email' => '',
            'password' => '',
        ];

        //act
        $response = $this->postJson(route('auth.register'), $data);

        //assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);

    });

it('should return a validation error if the password is too short in register', function () {
        //arrange
        $data = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => '1234',
        ];


        //act
        $response = $this->postJson(route('auth.register'), $data);

        //assert
        $response->assertStatus(422);
        $response->assertInvalid([
            'password' => 'The password field must be at least 8 characters.',
        ]);
    });




