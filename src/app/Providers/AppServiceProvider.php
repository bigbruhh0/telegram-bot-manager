<?php

namespace App\Providers;

use App\Repositories\BotRepository;
use App\Repositories\SubscriberRepository;
use App\Services\TelegraphService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BotRepository::class);
        $this->app->singleton(SubscriberRepository::class);
        $this->app->singleton(TelegraphService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Форсируем HTTPS для всех сгенерированных URL
        URL::forceScheme('https');
    }
}
