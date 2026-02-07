<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\OauthTokenServiceInterface;
use App\Services\KataraServices\OauthTokenService;
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
