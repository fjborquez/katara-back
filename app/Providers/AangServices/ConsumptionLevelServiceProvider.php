<?php

namespace App\Providers\AangServices;

use App\Contracts\Services\AangServices\ConsumptionLevelServiceInterface;
use App\Services\AangServices\ConsumptionLevelService;
use Illuminate\Support\ServiceProvider;

class ConsumptionLevelServiceProvider extends ServiceProvider
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
            ConsumptionLevelServiceInterface::class,
            ConsumptionLevelService::class
        );
    }
}
