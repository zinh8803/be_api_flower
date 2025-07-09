<?php

use App\Console\Commands\AutoCreateImportReceipt;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

app(Schedule::class)->command(AutoCreateImportReceipt::class)->everyMinute();
