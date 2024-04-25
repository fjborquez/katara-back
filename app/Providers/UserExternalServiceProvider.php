<?php

namespace App\Providers;

use App\Contracts\Services\UserExternalService\UserExternalServiceInterface;
use App\Services\UserExternalService\UserExternalService;
use Illuminate\Support\ServiceProvider;

class UserExternalServiceProvider extends ServiceProvider
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
            UserExternalServiceInterface::class,
            UserExternalService::class
        );
    }
}
