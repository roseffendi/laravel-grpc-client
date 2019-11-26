<?php

namespace App\Providers;

use App\Services\Grpc\GrpcUserProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Auth::provider('grpc', function ($app, array $config) {
            $clientFactory = $app->make(\App\Services\Grpc\Contracts\ClientFactory::class);
            $errorHandler = $app->make(\App\Services\Grpc\Contracts\ErrorHandler::class);

            return new GrpcUserProvider($clientFactory, $errorHandler);
        });
    }
}
