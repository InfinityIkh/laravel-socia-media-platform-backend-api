<?php

use App\Jobs\CountPostLikesJob;
use App\Jobs\CountPostRepostsJob;
use App\Jobs\CountPostSavesJob;
use App\Jobs\CountPostViewsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new CountPostViewsJob)->everyTwoHours();
Schedule::job(new CountPostLikesJob)->everyTwoHours();
Schedule::job(new CountPostRepostsJob)->everyTwoHours();
Schedule::job(new CountPostSavesJob)->everyTwoHours();
