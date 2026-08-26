<?php

namespace App\Services;

use InvalidArgumentException;

class AuthResolver
{
    //
    public function resolve(string $type): AuthStrategyInterface
    {
        //
        $class = match($type){
            'token' => TokenAuthService::class,
            'spa' => SpaAuthService::class,
            default => throw new InvalidArgumentException("Unsupported authentication type: {$type}")
        };
        return app($class);
    }
}
