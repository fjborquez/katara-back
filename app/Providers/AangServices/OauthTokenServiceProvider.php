<?php

namespace App\Providers\AangServices;

use App\Contracts\Services\AangServices\OauthTokenServiceInterface;
use App\Services\AangServices\OauthTokenService;
use Illuminate\Support\ServiceProvider;

class OauthTokenServiceProvider extends ServiceProvider
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
            OauthTokenServiceInterface::class,
            OauthTokenService::class
        );
    }
}
