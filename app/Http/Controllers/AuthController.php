<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\AuthResolver;
use App\Services\AuthServices;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(public AuthResolver $resolver){}

    public function register(AuthServices $authServices, UserRequest $request): JsonResponse
    {
        //
        $credentials = $request->validated();
        return $authServices->register($credentials);
    }

    public function login(UserRequest $request ,string $type): JsonResponse
    {
        //
        $credentials = $request->validated();
        $resolver = $this->resolver->resolve($type);
        return response()->json($resolver->login($credentials));
    }

    public function logout(string $type): JsonResponse
    {
        //
        $resolver = $this->resolver->resolve($type);
        $resolver->logout();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
