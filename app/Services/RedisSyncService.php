<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Redis;

use function PHPUnit\Framework\callback;

class RedisSyncService 
{
    public function syncRedisSet(string $pattern , Closure $callback){
        $cursor = null;
        do{
            $keys = Redis::scan($cursor , ['MATCH' => $pattern , 'COUNT' => 100]);
            if($keys === false)continue;
            foreach($keys as $key){
                $post_id = explode(':',$key)[1];
                $usersIds = Redis::sMembers($key);
                $callback($post_id ,$usersIds);
                Redis::del($key);
            }
        }while($cursor != 0);
    }
}