<?php

namespace App\Providers\AangServices;

use App\Contracts\Services\AangServices\HouseServiceInterface;
use App\Services\AangServices\HouseService;
use Illuminate\Support\ServiceProvider;

class HouseServiceProvider extends ServiceProvider
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
            HouseServiceInterface::class,
            HouseService::class
        );
    }
}
