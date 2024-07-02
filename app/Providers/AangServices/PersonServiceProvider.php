<?php

namespace App\Providers\AangServices;

use App\Contracts\Services\AangServices\PersonServiceInterface;
use App\Services\AangServices\PersonService;
use Illuminate\Support\ServiceProvider;

class PersonServiceProvider extends ServiceProvider
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
            PersonServiceInterface::class,
            PersonService::class
        );
    }
}
