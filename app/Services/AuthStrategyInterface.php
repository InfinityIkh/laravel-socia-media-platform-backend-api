<?php

namespace App\Services;

use App\Http\Requests\UserRequest;

interface AuthStrategyInterface
{
    //
    public function login(array $credentials): array;

    public function logout(): void;
}
