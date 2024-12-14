<?php

namespace App\Providers\ZukoServices;

use App\Contracts\Services\ZukoServices\ProductBrandServiceInterface;
use App\Services\ZukoServices\ProductBrandService;
use Illuminate\Support\ServiceProvider;

class ProductBrandServiceProvider extends ServiceProvider
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
            ProductBrandServiceInterface::class,
            ProductBrandService::class
        );
    }
}
