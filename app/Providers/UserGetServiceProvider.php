<?php

namespace App\Providers;

use App\Contracts\Services\UserGetService\UserGetServiceInterface;
use App\Services\UserGetService\UserGetService;
use Illuminate\Support\ServiceProvider;

class UserGetServiceProvider extends ServiceProvider
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
            UserGetServiceInterface::class,
            UserGetService::class
        );
    }
}
