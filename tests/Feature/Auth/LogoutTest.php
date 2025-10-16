<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
uses()->group('auth');

it('should successfully log out an authenticated user', function () {


    //arrange
    $user = User::factory()->create();
    $token = $user->createToken('default')->plainTextToken;

    //act
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->postJson(route('auth.logout'));


    //assert
    $response
        ->assertNoContent();

    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});

it('should return an unauthorized error if an unauthenticated user attempts to log out', function () {
    //act
    $response = $this->postJson(route('auth.logout'));

    // Assert: Verificar se a resposta é 401 Unauthorized
    $response->assertUnauthorized();
});

