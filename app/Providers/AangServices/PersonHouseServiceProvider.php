<?php

namespace App\Providers\AangServices;

use App\Contracts\Services\AangServices\PersonHouseServiceInterface;
use App\Services\AangServices\PersonHouseService;
use Illuminate\Support\ServiceProvider;

class PersonHouseServiceProvider extends ServiceProvider
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
            PersonHouseServiceInterface::class,
            PersonHouseService::class
        );
    }
}
