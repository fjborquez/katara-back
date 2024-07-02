<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\NutritionalProfileServiceInterface;
use App\Services\KataraServices\NutritionalProfileService;
use Illuminate\Support\ServiceProvider;

class NutritionalProfileServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(
            NutritionalProfileServiceInterface::class,
            NutritionalProfileService::class
        );
    }
}
