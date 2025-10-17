<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Auth;

class AuthService
{
    public function register(array $data): array
    {

        $user = User::create($data);

        $token = $user->createToken('default')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $credentials): ?array
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = Auth::user();
        $token = $user->createToken('default')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
