<?php

namespace App\Providers\AangServices;

use App\Contracts\Services\AangServices\UserServiceInterface;
use App\Services\AangServices\UserService;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
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
            UserServiceInterface::class,
            UserService::class
        );
    }
}
