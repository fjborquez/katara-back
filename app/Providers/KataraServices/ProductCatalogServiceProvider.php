<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\ProductCatalogServiceInterface;
use App\Services\KataraServices\ProductCatalogService;
use Illuminate\Support\ServiceProvider;

class ProductCatalogServiceProvider extends ServiceProvider
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
            ProductCatalogServiceInterface::class,
            ProductCatalogService::class
        );
    }
}
