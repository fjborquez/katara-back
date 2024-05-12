<?php

namespace App\Providers;

use App\Contracts\Services\UserHousesGetService\UserHousesGetServiceInterface;
use App\Services\UserHousesGetService\UserHousesGetService;
use Illuminate\Support\ServiceProvider;

class UserHousesGetServiceProvider extends ServiceProvider
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
            UserHousesGetServiceInterface::class,
            UserHousesGetService::class
        );
    }
}
