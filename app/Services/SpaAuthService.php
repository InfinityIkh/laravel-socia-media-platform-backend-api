<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class SpaAuthService implements AuthStrategyInterface
{
    //
    public function login(array $credentials): array
    {
        //
        if (!Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ])) {
            return [
                'status' => 401,
                'body' => [
                    'message' => 'Invalid credentials'
                ]
            ];
        }

        request()->session()->regenerate();

        return [
            'status' => 200,
            'body' => [
                'message' => 'Login successful.',
                'user' => request()->user(),
            ]
        ];
    }

    public function logout(): void
    {
        //
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

    }
}
