<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your console command routes.
| Each route is bound to a command instance, allowing you to easily
| execute any command from the command line or via the scheduler.
*/

Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');

// Расписание задач
Schedule::command('order:update-status-from-yandex-delivery')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->evenInMaintenanceMode();

Schedule::command('payments:process-sellers')
    ->hourly()
    ->withoutOverlapping()
    ->evenInMaintenanceMode();

Schedule::command('app:get-payment-state')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->evenInMaintenanceMode();

Schedule::command('self-employed:check-status')
    ->everyMinute()
    ->withoutOverlapping()
    ->evenInMaintenanceMode();

Schedule::command('payments:calculate')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->evenInMaintenanceMode();
