<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\ProductPresentationServiceInterface;
use App\Services\KataraServices\ProductPresentationService;
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
