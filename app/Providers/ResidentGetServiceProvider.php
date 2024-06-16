<?php

namespace App\Providers;

use App\Contracts\Services\ResidentGetService\ResidentGetServiceInterface;
use App\Services\ResidentGetService\ResidentGetService;
use Illuminate\Support\ServiceProvider;

class ResidentGetServiceProvider extends ServiceProvider
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
            ResidentGetServiceInterface::class,
            ResidentGetService::class
        );
    }
}
