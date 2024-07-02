<?php

namespace App\Providers\AangServices;

use App\Contracts\Services\AangServices\NutritionalRestrictionServiceInterface;
use App\Services\AangServices\NutritionalRestrictionService;
use Illuminate\Support\ServiceProvider;

class NutritionalRestrictionServiceProvider extends ServiceProvider
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
            NutritionalRestrictionServiceInterface::class,
            NutritionalRestrictionService::class
        );
    }
}
