<?php

namespace App\Providers;

use App\Contracts\Services\UserHouseCreateService\UserHouseCreateServiceInterface;
use App\Services\UserHouseCreateService\UserHouseCreateService;
use Illuminate\Support\ServiceProvider;

class UserHouseCreateServiceProvider extends ServiceProvider
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
            UserHouseCreateServiceInterface::class,
            UserHouseCreateService::class
        );
    }
}
