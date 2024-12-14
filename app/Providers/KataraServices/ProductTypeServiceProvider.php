<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\ProductTypeServiceInterface;
use App\Services\KataraServices\ProductTypeService;
use Illuminate\Support\ServiceProvider;

class ProductTypeServiceProvider extends ServiceProvider
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
            ProductTypeServiceInterface::class,
            ProductTypeService::class
        );
    }
}
