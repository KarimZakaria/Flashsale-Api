<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\HoldRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(HoldRepository::class, function ($app) {
            return new HoldRepository();
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
