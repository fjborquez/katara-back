<?php

namespace App\Providers\AangServices;

use App\Contracts\Services\AangServices\NutritionalProfileServiceInterface;
use App\Services\AangServices\NutritionalProfileService;
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
