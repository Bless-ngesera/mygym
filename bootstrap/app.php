<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckUserRole;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\RateLimitChat;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Database\QueryException;
use Psr\Log\LogLevel;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register aliases for middleware
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'role' => CheckUserRole::class,
            'setlocale' => SetLocale::class,
            'chat.rate.limit' => RateLimitChat::class,
        ]);

        // Global middleware (runs for every request)
        $middleware->use([
            \Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks::class,
            \Illuminate\Http\Middleware\TrustHosts::class,
            \Illuminate\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        // Web middleware group
        $middleware->web(append: [
            SetLocale::class,
        ]);

        // Ensure web group has all required middleware
        $middleware->web(replace: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // API middleware group
        $middleware->api(prepend: [
            'throttle:api',
        ]);

        $middleware->api(append: [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Priority middleware (order matters)
        $middleware->priority([
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            \Illuminate\Auth\Middleware\Authorize::class,
            CheckUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Custom exception rendering for 404
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found',
                    'error' => $e->getMessage()
                ], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // Custom exception rendering for authentication errors
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                    'error' => 'Please login to continue'
                ], 401);
            }
            return redirect()->route('login');
        });

        // Custom exception rendering for access denied
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                    'error' => 'You do not have permission to access this resource'
                ], 403);
            }

            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                    'error' => 'You do not have permission to access this resource'
                ], 403);
            }

            return redirect()->back()->with('error', 'You do not have permission to access this resource.');
        });

        // Custom rate limit exception handling for chat
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, Request $request) {
            if ($request->is('chat/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Too many requests',
                    'message' => 'You have exceeded the rate limit. Please wait before sending more messages.',
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? 60
                ], 429);
            }

            return response()->view('errors.429', [], 429);
        });

        // Set log levels for specific exceptions
        $exceptions->level(QueryException::class, LogLevel::CRITICAL);
        $exceptions->level(AuthenticationException::class, LogLevel::WARNING);
        $exceptions->level(AccessDeniedHttpException::class, LogLevel::WARNING);
        $exceptions->level(NotFoundHttpException::class, LogLevel::INFO);
        $exceptions->level(\Illuminate\Http\Exceptions\ThrottleRequestsException::class, LogLevel::INFO);

        // Reportable exceptions
        $exceptions->reportable(function (Throwable $e) {
            if (!$e instanceof \Illuminate\Validation\ValidationException) {
                \Illuminate\Support\Facades\Log::error('Exception: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);

                if (app()->bound('sentry')) {
                    app('sentry')->captureException($e);
                }
            }
        });

        // Don't report certain exceptions
        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
            \Illuminate\Database\Eloquent\ModelNotFoundException::class,
            \Illuminate\Validation\ValidationException::class,
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
            \Illuminate\Http\Exceptions\ThrottleRequestsException::class,
        ]);

        // Handle throttling exceptions gracefully
        $exceptions->throttle(function (Throwable $e) {
            return $e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException;
        });
    })
    ->withSchedule(function (Schedule $schedule): void {
        // ==================== NOTIFICATION COMMANDS ====================

        // Workout reminders - every hour (2 hours before workout)
        $schedule->command('notifications:workout-reminders --hours=2')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/notifications-workout.log'));

        // Class reminders - 24 hours before (daily at 9 AM)
        $schedule->command('notifications:class-reminders --hours=24')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/notifications-class-24h.log'));

        // Class reminders - 1 hour before (every hour)
        $schedule->command('notifications:class-reminders --hours=1')
            ->hourlyAt(0) // Run at minute 0 of every hour
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/notifications-class-1h.log'));

        // Daily motivation - every day at 6 AM
        $schedule->command('notifications:daily-motivation')
            ->dailyAt('06:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/notifications-motivation.log'));

        // Weekly reports - every Sunday at 8 PM
        $schedule->command('notifications:weekly-reports')
            ->sundays()
            ->at('20:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/notifications-reports.log'));

        // Check expiring subscriptions - daily at 9 AM
        $schedule->command('notifications:check-subscriptions --days=30')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/notifications-subscriptions.log'));

        // Streak reminders - daily at 7 PM
        $schedule->command('notifications:streak-reminders')
            ->dailyAt('19:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/notifications-streak.log'));

        // Clean expired notifications - daily at 2 AM
        $schedule->command('notifications:clean --days=30 --force')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/notifications-clean.log'));

        // Process pending push notifications - every 30 minutes
        $schedule->command('notifications:process-push --limit=100')
            ->everyThirtyMinutes()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/notifications-push.log'));

        // ==================== SYSTEM MAINTENANCE COMMANDS ====================

        // Cache cleanup (every 6 hours)
        $schedule->command('cache:prune-stale-tags')
            ->everySixHours()
            ->withoutOverlapping()
            ->runInBackground();

        // Clean up old logs weekly (Sundays at 2 AM)
        $schedule->command('log:clear')
            ->weekly()
            ->sundays()
            ->at('02:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/log-cleanup.log'));

        // Clean up old session files (hourly)
        $schedule->command('session:clean')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();

        // ==================== PRODUCTION-ONLY COMMANDS ====================

        if (app()->environment('production')) {
            // Database backup (daily at 1 AM) - only if backup command exists
            try {
                $schedule->command('backup:run --only-db')
                    ->dailyAt('01:00')
                    ->withoutOverlapping()
                    ->runInBackground()
                    ->appendOutputTo(storage_path('logs/backup.log'));
            } catch (\Exception $e) {
                // Backup command not available, skip
            }

            // Queue worker restart (daily at 3 AM)
            $schedule->command('queue:restart')
                ->dailyAt('03:00')
                ->withoutOverlapping();

            // Telescope prune (daily at 4 AM) - if using Telescope
            if (class_exists(\Laravel\Telescope\Telescope::class)) {
                $schedule->command('telescope:prune')
                    ->dailyAt('04:00')
                    ->withoutOverlapping();
            }

            // Horizon snapshot (every 5 minutes) - if using Horizon
            if (class_exists(\Laravel\Horizon\Horizon::class)) {
                $schedule->command('horizon:snapshot')
                    ->everyFiveMinutes()
                    ->withoutOverlapping();
            }
        }

        // ==================== LOCAL DEVELOPMENT COMMANDS ====================

        if (app()->environment('local')) {
            // Run notification tests daily at noon (dry run)
            $schedule->command('notifications:daily-motivation --role=admin --dry-run')
                ->dailyAt('12:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/notifications-test.log'));
        }
    })
    ->create();
