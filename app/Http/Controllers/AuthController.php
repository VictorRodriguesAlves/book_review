<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function register(RegisterRequest $request): jsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 201);

    }

    public function login(LoginRequest $request): jsonResponse
    {

        $result = $this->authService->login($request->validated());

        if (is_null($result)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    public function logout(Request $request): Response
    {
        $this->authService->logout($request->user());

        return response()->noContent();
    }

}
