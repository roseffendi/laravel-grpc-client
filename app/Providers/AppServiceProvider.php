<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Services\Grpc\Contracts\ClientFactory::class,
            \App\Services\Grpc\ConfigurableClientFactory::class
        );

        $this->app->bind(
            \App\Services\Grpc\Contracts\ErrorHandler::class,
            \App\Services\Grpc\LaravelErrorHandler::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
