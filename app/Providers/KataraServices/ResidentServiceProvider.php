<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\ResidentServiceInterface;
use App\Services\KataraServices\ResidentService;
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
