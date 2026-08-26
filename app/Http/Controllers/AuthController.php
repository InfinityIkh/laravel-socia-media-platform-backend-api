<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\AuthResolver;
use App\Services\AuthServices;

class AuthController extends Controller
{
    public function __construct(public AuthResolver $resolver){}

    public function register(AuthServices $authServices, UserRequest $request){
        //
        $credentials = $request->validated();
        return $authServices->register($credentials);
    }

    public function login(UserRequest $request ,string $type){
        //
        $resolver = $this->resolver->resolve($type);
        return $resolver->login($request);
    }

    public function logout(UserRequest $request ,string $type){
        //
        $resolver = $this->resolver->resolve($type);
        return $resolver->logout($request);
    }
}
