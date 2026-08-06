<?php

namespace App\Jobs;

use App\Models\PostLike;
use App\Services\RedisSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Redis;

class CountPostLikesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(RedisSyncService $redisSyncService): void
    {
        //
        $redisSyncService->syncRedisSet('post:*:likes' ,function($post_id ,$usersIds){
            if(empty($usersIds))return;
            $now = now();
            $rows = [];
            foreach($usersIds as $userId){
                $rows[] = [
                    'post_id' => $post_id,
                    'user_id' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            PostLike::insertOrIgnore($rows);
        });

        $redisSyncService->syncRedisSet('post:*:unlikes' ,function($post_id ,$usersIds){
            if(empty($usersIds))return;
            PostLike::where('post_id',$post_id)
                    ->whereIn('user_id',$usersIds)
                    ->delete();
        });
    }
}
