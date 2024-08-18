<?php

namespace App\Providers\ZukoServices;

use App\Contracts\Services\ZukoServices\ProductCategoryServiceInterface;
use App\Services\ZukoServices\ProductCategoryService;
use Illuminate\Support\ServiceProvider;

class ProductCategoryServiceProvider extends ServiceProvider
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
            ProductCategoryServiceInterface::class,
            ProductCategoryService::class
        );
    }
}
