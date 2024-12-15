<?php

namespace App\Providers\KataraServices;

use App\Contracts\Services\KataraServices\GoogleCloudLogWriterServiceInterface;
use App\Services\KataraServices\GoogleCloudLogWriterService;
use Illuminate\Support\ServiceProvider;

class GoogleCloudLogWriterServiceProvider extends ServiceProvider
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
            GoogleCloudLogWriterServiceInterface::class,
            GoogleCloudLogWriterService::class
        );
    }
}
