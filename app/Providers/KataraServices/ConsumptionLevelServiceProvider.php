<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\ConsumptionLevelServiceInterface;
use App\Services\KataraServices\ConsumptionLevelService;
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
