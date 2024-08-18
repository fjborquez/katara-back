<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\ProductCategoryServiceInterface;
use App\Services\KataraServices\ProductCategoryService;
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
