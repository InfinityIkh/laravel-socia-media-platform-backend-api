<?php

namespace App\Services;

use App\Http\Requests\UserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SpaAuthService implements AuthStrategyInterface
{
    //
    public function login(UserRequest $request): JsonResponse
    {
        //
        $credentials = $request->validated();
        if (!Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ])) {
            return response()->json([
                'message' => 'Invalid credentials.'
            ], 401);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login successful.',
            'user' => $request->user(),
        ]);
    }

    public function logout(UserRequest $request): JsonResponse
    {
        //
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout successful.'
        ]);
    }
}
