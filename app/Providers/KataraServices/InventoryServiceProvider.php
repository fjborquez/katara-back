<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\InventoryServiceInterface;
use App\Services\KataraServices\InventoryService;
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
