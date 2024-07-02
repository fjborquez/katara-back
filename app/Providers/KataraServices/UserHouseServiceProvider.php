<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\UserHouseServiceInterface;
use App\Services\KataraServices\UserHouseService;
use Illuminate\Support\ServiceProvider;

class UserHouseServiceProvider extends ServiceProvider
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
            UserHouseServiceInterface::class,
            UserHouseService::class
        );
    }
}
