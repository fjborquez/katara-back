<?php

namespace App\Providers;

use App\Contracts\Services\UserActivationService\UserActivationServiceInterface;
use App\Http\Controllers\UserActivation;
use Illuminate\Support\ServiceProvider;

class UserActivationServiceProvider extends ServiceProvider
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
            UserActivationServiceInterface::class,
            UserActivation::class
        );
    }
}
