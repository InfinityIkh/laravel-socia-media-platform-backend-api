<?php

namespace App\Services;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class TokenAuthService implements AuthStrategyInterface
{
    //
    public function login(UserRequest $request): JsonResponse
    {
        //
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();
        if (!$user ||!Hash::check($credentials['password'], $user->password))
        {
            return response()->json([
                'message' => 'Invalid credentials.'
            ], 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(UserRequest $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful.'
        ]);
    }

}
