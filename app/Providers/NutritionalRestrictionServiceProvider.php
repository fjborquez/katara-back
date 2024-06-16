<?php

namespace App\Providers;

use App\Contracts\Services\NutritionalRestrictionService\NutritionalRestrictionServiceInterface;
use App\Services\NutritionalRestrictionService\NutritionalRestrictionService;
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
