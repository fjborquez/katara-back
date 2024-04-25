<?php

namespace App\Providers;

use App\Contracts\Services\UserRegistrationService\UserRegistrationServiceInterface;
use App\Services\UserRegistrationService\UserRegistrationService;
use Illuminate\Support\ServiceProvider;

class UserRegistrationServiceProvider extends ServiceProvider
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
            UserRegistrationServiceInterface::class,
            UserRegistrationService::class
        );
    }
}
