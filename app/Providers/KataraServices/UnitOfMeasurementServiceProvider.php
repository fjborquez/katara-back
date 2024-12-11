<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\UnitOfMeasurementServiceInterface;
use App\Services\KataraServices\UnitOfMeasurementService;
use Illuminate\Support\ServiceProvider;

class UnitOfMeasurementServiceProvider extends ServiceProvider
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
            UnitOfMeasurementServiceInterface::class,
            UnitOfMeasurementService::class
        );
    }
}
