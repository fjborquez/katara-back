<?php

namespace App\Providers;

use App\Contracts\Services\NutritionalProfileService\NutritionalProfileServiceInterface;
use App\Services\NutritionalProfileService\NutritionalProfileService;
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
