<?php

namespace App\Jobs;

use App\Models\PostSave;
use App\Services\RedisSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CountPostSavesJob implements ShouldQueue
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
        $redisSyncService->syncRedisSet('post:*:saves',function($post_id ,$usersIds){
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
            PostSave::insertOrIgnore($rows);
        });

        $redisSyncService->syncRedisSet('post:*:unsaves',function($post_id ,$usersIds){
            if(empty($usersIds))return;
            PostSave::where('post_id',$post_id)
                    ->whereIn('user_id',$usersIds)
                    ->delete();
        });
    }
}
