<?php

namespace App\Providers\AangServices;

use App\Contracts\Services\AangServices\CityServiceInterface;
use App\Services\AangServices\CityService;
use Illuminate\Support\ServiceProvider;

class CityServiceProvider extends ServiceProvider
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
            CityServiceInterface::class,
            CityService::class
        );
    }
}
