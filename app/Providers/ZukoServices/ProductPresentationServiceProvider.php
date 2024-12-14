<?php

namespace App\Providers\ZukoServices;

use App\Contracts\Services\ZukoServices\ProductPresentationServiceInterface;
use App\Services\ZukoServices\ProductPresentationService;
use Illuminate\Support\ServiceProvider;

class ProductPresentationServiceProvider extends ServiceProvider
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
            ProductPresentationServiceInterface::class,
            ProductPresentationService::class
        );
    }
}
