<?php

namespace App\Providers\AzulaServices;

use App\Contracts\Services\AzulaServices\InventoryServiceInterface;
use App\Services\AzulaServices\InventoryService;
use Illuminate\Support\ServiceProvider;

class InventoryServiceProvider extends ServiceProvider
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
            InventoryServiceInterface::class,
            InventoryService::class
        );
    }
}
