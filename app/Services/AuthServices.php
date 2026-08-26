<?php

namespace App\Services;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthServices
{
    //
    public function register(array $credentials): JsonResponse
    {
        $user = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password'=> Hash::make($credentials['password']),
            'role' => $credentials['role'] ?? 'user'
        ]);
        return response()->json([
            'user' => new UserResource($user),
        ]);
    }
}
