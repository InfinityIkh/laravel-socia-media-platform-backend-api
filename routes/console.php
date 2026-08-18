<?php

use App\Jobs\CountPostLikesJob;
use App\Jobs\CountPostRepostsJob;
use App\Jobs\CountPostSavesJob;
use App\Jobs\CountPostViewsJob;
use App\Jobs\DeleteExpiredStoriesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new CountPostViewsJob)->everyTenMinutes();
Schedule::job(new CountPostLikesJob)->everyTenMinutes();
Schedule::job(new CountPostRepostsJob)->everyTenMinutes();
Schedule::job(new CountPostSavesJob)->everyTenMinutes();

Schedule::job(new DeleteExpiredStoriesJob)->everyMinute();