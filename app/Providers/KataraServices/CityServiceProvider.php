<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\CityServiceInterface;
use App\Services\KataraServices\CityService;
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
