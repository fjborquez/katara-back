<?php

namespace App\Providers;

use App\Contracts\Services\ResidentListService\ResidentListServiceInterface;
use App\Services\ResidentListService\ResidentListService;
use Illuminate\Support\ServiceProvider;

class ResidentListServiceProvider extends ServiceProvider
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
            ResidentListServiceInterface::class,
            ResidentListService::class
        );
    }
}
