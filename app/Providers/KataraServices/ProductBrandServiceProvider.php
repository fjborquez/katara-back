<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\ProductBrandServiceInterface;
use App\Services\KataraServices\ProductBrandService;
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
