<?php

namespace App\Providers;

use App\Contracts\Services\UserUpdateService\UserUpdateServiceInterface;
use App\Services\UserUpdateService\UserUpdateService;
use Illuminate\Support\ServiceProvider;

class UserUpdateServiceProvider extends ServiceProvider
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
            UserUpdateServiceInterface::class,
            UserUpdateService::class
        );
    }
}
