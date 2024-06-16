<?php

namespace App\Providers;

use App\Contracts\Services\UserHouseUpdateService\UserHouseUpdateServiceInterface;
use App\Services\UserHouseUpdateService\UserHouseUpdateService;
use Illuminate\Support\ServiceProvider;

class UserHouseUpdateServiceProvider extends ServiceProvider
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
            UserHouseUpdateServiceInterface::class,
            UserHouseUpdateService::class
        );
    }
}
