<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\NutritionalRestrictionServiceInterface;
use App\Services\KataraServices\NutritionalRestrictionService;
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
