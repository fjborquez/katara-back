<?php

namespace App\Providers\ZukoServices;

use App\Contracts\Services\ZukoServices\ProductTypeServiceInterface;
use App\Services\ZukoServices\ProductTypeService;
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
