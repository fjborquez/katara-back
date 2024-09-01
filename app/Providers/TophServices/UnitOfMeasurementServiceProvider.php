<?php

namespace App\Providers\TophServices;

use App\Contracts\Services\TophServices\UnitOfMeasurementServiceInterface;
use App\Services\TophServices\UnitOfMeasurementService;
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
