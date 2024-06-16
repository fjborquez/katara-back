<?php

namespace App\Providers;

use App\Contracts\Services\PersonHouseUpdateService\PersonHouseUpdateServiceInterface;
use App\Services\PersonHouseUpdateService\PersonHouseUpdateService;
use Illuminate\Support\ServiceProvider;

class PersonHouseUpdateServiceProvider extends ServiceProvider
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
            PersonHouseUpdateServiceInterface::class,
            PersonHouseUpdateService::class
        );
    }
}
