<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): jsonResponse
    {
        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'user_type' => 'user',
        ]);

        Auth::login($user);

        return $this->generateTokenResponse($user, 201);

    }

    public function login(LoginRequest $request): jsonResponse
    {

        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        return $this->generateTokenResponse(Auth::user());
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    protected function generateTokenResponse(User $user, int $statusCode = 200): JsonResponse
    {

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->createToken('default')->plainTextToken,
        ], $statusCode);

    }
}
