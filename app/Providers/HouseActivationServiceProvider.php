<?php

namespace App\Providers;

use App\Contracts\Services\HouseActivationService\HouseActivationServiceInterface;
use App\Services\HouseActivationService\HouseActivationService;
use Illuminate\Support\ServiceProvider;

class HouseActivationServiceProvider extends ServiceProvider
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
            HouseActivationServiceInterface::class,
            HouseActivationService::class
        );
    }
}
