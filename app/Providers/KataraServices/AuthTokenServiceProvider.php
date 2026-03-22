<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\AuthTokenServiceInterface;
use App\Services\KataraServices\AuthTokenService;
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
