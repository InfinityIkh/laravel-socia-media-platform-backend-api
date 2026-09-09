<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TokenAuthService implements AuthStrategyInterface
{
    //
    public function login(array $credentials): array
    {
        //
        $user = User::where('email', $credentials['email'])->first();
        if (!$user ||!Hash::check($credentials['password'], $user->password))
        {
            return [
                'status' => 401,
                'body' => [
                    'message' => 'Invalid credentials'
                ]
            ];
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'status' => 200,
            'body' => [
                'message' => 'Login successful.',
                'user' => new UserResource($user),
                'token' => $token,
            ]
        ];
    }

    public function logout(): void
    {
        request()->user()->currentAccessToken()->delete();
    }

}
