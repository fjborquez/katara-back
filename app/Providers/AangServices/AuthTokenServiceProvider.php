<?php

namespace App\Providers\AangServices;

use App\Contracts\Services\AangServices\AuthTokenServiceInterface;
use App\Services\AangServices\AuthTokenService;
use Illuminate\Support\ServiceProvider;

class AuthTokenServiceProvider extends ServiceProvider
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
            AuthTokenServiceInterface::class,
            AuthTokenService::class
        );
    }
}
