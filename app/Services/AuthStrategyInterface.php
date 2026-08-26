<?php

namespace App\Services;

use App\Http\Requests\UserRequest;

interface AuthStrategyInterface
{
    //
    public function login(UserRequest $request);

    public function logout(UserRequest $request);
}
