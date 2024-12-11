<?php

namespace App\Providers\ZukoServices;

use App\Contracts\Services\ZukoServices\ProductCatalogServiceInterface;
use App\Services\ZukoServices\ProductCatalogService;
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
