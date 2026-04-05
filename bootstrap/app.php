<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckUserRole;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Send class reminders for classes in 2 hours (every 30 minutes)
        $schedule->command('reminders:send')->everyThirtyMinutes();

        // Send reminders for tomorrow's classes (daily at 8 PM)
        $schedule->command('reminders:tomorrow')->dailyAt('20:00');

        // Clean up old logs weekly
        // $schedule->command('log:clear')->weekly();
    })
    ->create();
