<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use App\Services\RedisSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Redis;

class CountPostViewsJob implements ShouldQueue
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
        $redisSyncService->syncRedisSet('post:*:viewers' ,function($post_id ,$usersIds){
            if(empty($usersIds))return;
            $now = now();
            $rows = [];
                foreach ($usersIds as $viewerId) {
                    $rows[] = [
                        'user_id' => $viewerId,
                        'post_id' => $post_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            PostView::insertOrIgnore($rows);
        });
    }
}
