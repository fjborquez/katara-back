<?php

namespace App\Providers\AangServices;

use App\Contracts\Services\AangServices\ResidentServiceInterface;
use App\Services\AangServices\ResidentService;
use Illuminate\Support\ServiceProvider;

class ResidentServiceProvider extends ServiceProvider
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
            ResidentServiceInterface::class,
            ResidentService::class
        );
    }
}
