<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AIChatService;

class AIChatServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AIChatService::class, function ($app) {
            return new AIChatService();
        });
    }

    public function boot(): void
    {
        //
    }
}
