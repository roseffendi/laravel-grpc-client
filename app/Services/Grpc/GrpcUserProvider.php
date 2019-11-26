<?php

namespace App\Services\Grpc;

use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use App\Services\Grpc\Contracts\ErrorHandler;
use App\Services\Grpc\Contracts\ClientFactory;
use Protobuf\Identity\LoginRequest;
use Protobuf\Identity\UserByEmailRequest;
use Protobuf\Identity\UserByIdRequest;
use Protobuf\Identity\UserResponse;

class GrpcUserProvider implements UserProvider
{
    /**
     * Error handler.
     * 
     * @var \App\Services\Grpc\Contracts\ErrorHandler
     */
    protected $errorHandler;

    /**
     * Auth service client.
     * 
     * @var mixed
     */
    protected $authServiceClient;

    /**
     * Create new instance.
     * 
     * @param   \App\Services\Grpc\Contracts\ClientFactory  $grpcClientFactory
     * @param   \App\Services\Grpc\Contracts\ErrorHandler   $errorHandler
     */
    public function __construct(ClientFactory $grpcClientFactory, ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;

        $this->authServiceClient = $grpcClientFactory->make(\Protobuf\Identity\AuthServiceClient::class);
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $request = new UserByIdRequest;

        $request->setId($identifier);
        [$response, $status] = $this->authServiceClient->UserById($request)->wait();

        $this->errorHandler->handle($status, 3);

        return $this->generateAuthenticable($response);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // Implemented later
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        // Implemented later
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $request = new LoginRequest();

        $request->setEmail($credentials['email']);
        $request->setPassword($credentials['password']);

        [$response, $status] = $this->authServiceClient->Login($request)->wait();

        $this->errorHandler->handle($status, 3);
        
        return $this->generateAuthenticable($response);
    }

    /**
     * Retrieve a user by given email.
     * 
     * @param  string  $email
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByEmail(string $email)
    {
        $request = new UserByEmailRequest();

        $request->setEmail($email);

        [$response, $status] = $this->authServiceClient->UserByEmail($request)->wait();

        $this->errorHandler->handle($status, 3);

        return $this->generateAuthenticable($response);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Implemented later
    }

    /**
     * Generate authenticable.
     * 
     * @param   \Protobuf\Identity\UserResponse             $userResponse
     * 
     * @return  \Illuminate\Contracts\Auth\Authenticatable
     */
    protected function generateAuthenticable(UserResponse $userResponse)
    {
        $user = new User;

        $user->id = $userResponse->getId();
        $user->email = $userResponse->getEmail();
        $user->name = $userResponse->getName();

        return $user;
    }
}