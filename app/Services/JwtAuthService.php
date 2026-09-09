<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class JwtAuthService implements AuthStrategyInterface
{
    //
    public function login(array $credentials): array
    {
        //
        if (!$token = auth('api')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'message' => ['Invalid credentials.'],
            ]);
        }

        return [
            'status' => 200,
            'body' => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth('api')->factory()->getTTL() * 60,
            ]
        ];
    }

    public function logout(): void
    {
        //
        auth('api')->logout();
    }
}
