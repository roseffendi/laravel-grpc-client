<?php

namespace Tests\Unit;

use App\Services\Grpc\GrpcUserProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Protobuf\Identity\UserResponse;
use stdClass;
use Tests\TestCase;

class GrpcUserProviderTest extends TestCase
{
    protected $grpcClientFactory;

    protected $errorHandler;    

    public function setUp(): void
    {
        parent::setUp();

        $this->errorHandler = $this->mock(\App\Services\Grpc\Contracts\ErrorHandler::class);
        $this->grpcClientFactory = $this->mock(\App\Services\Grpc\Contracts\ClientFactory::class);
    }

    public function testItIsUserProvider()
    {
        $this->grpcClientFactory->shouldReceive('make')->once();

        $grpcUserProvider = new GrpcUserProvider($this->grpcClientFactory, $this->errorHandler);

        $this->assertTrue($grpcUserProvider instanceof \Illuminate\Contracts\Auth\UserProvider);
    }

    public function testItCanFindUserById()
    {
        $authServiceClient = $this->mock(\Protobuf\Identity\AuthServiceClient::class);
        $baseStub = $this->mock(\Grpc\BaseStub::class); // It's a dummy class
        $userResponse = new UserResponse;

        $userResponse->setId(1);
        $userResponse->setEmail('email@email.com');
        $userResponse->setName('full name');
        
        $this->errorHandler->shouldReceive('handle');
        $this->grpcClientFactory->shouldReceive('make')->once()->andReturn($authServiceClient);

        $baseStub->shouldReceive('wait')->andReturn([
            $userResponse,
            [
                'code' => 0,
                'descriptions' => ''
            ]
        ]);

        $authServiceClient->shouldReceive('UserById')->andReturn($baseStub);
        
        $grpcUserProvider = new GrpcUserProvider($this->grpcClientFactory, $this->errorHandler);

        $user = $grpcUserProvider->retrieveById(1);

        $this->assertTrue($user instanceof \Illuminate\Contracts\Auth\Authenticatable);
        $this->assertSame($user->id, 1);
    }

    public function testItCanFindUserByCredentials()
    {
        $authServiceClient = $this->mock(\Protobuf\Identity\AuthServiceClient::class);
        $baseStub = $this->mock(\Grpc\BaseStub::class); // It's a dummy class
        $userResponse = new UserResponse;

        $userResponse->setId(1);
        $userResponse->setEmail('email@email.com');
        $userResponse->setName('full name');
        
        $this->errorHandler->shouldReceive('handle');
        $this->grpcClientFactory->shouldReceive('make')->once()->andReturn($authServiceClient);

        $baseStub->shouldReceive('wait')->andReturn([
            $userResponse,
            [
                'code' => 0,
                'descriptions' => ''
            ]
        ]);

        $authServiceClient->shouldReceive('Login')->andReturn($baseStub);
        
        $grpcUserProvider = new GrpcUserProvider($this->grpcClientFactory, $this->errorHandler);

        $user = $grpcUserProvider->retrieveByCredentials([
            'email' => 'email@email.com',
            'password' => '123456'
        ]);

        $this->assertTrue($user instanceof \Illuminate\Contracts\Auth\Authenticatable);
        $this->assertSame($user->id, 1);
    }

    public function testItCanFindUserByEmail()
    {
        $authServiceClient = $this->mock(\Protobuf\Identity\AuthServiceClient::class);
        $baseStub = $this->mock(\Grpc\BaseStub::class); // It's a dummy class
        $userResponse = new UserResponse;

        $userResponse->setId(1);
        $userResponse->setEmail('email@email.com');
        $userResponse->setName('full name');
        
        $this->errorHandler->shouldReceive('handle');
        $this->grpcClientFactory->shouldReceive('make')->once()->andReturn($authServiceClient);

        $baseStub->shouldReceive('wait')->andReturn([
            $userResponse,
            [
                'code' => 0,
                'descriptions' => ''
            ]
        ]);

        $authServiceClient->shouldReceive('UserByEmail')->andReturn($baseStub);
        
        $grpcUserProvider = new GrpcUserProvider($this->grpcClientFactory, $this->errorHandler);

        $user = $grpcUserProvider->retrieveByEmail('email@email.com');

        $this->assertTrue($user instanceof \Illuminate\Contracts\Auth\Authenticatable);
        $this->assertSame($user->id, 1);
    }
}
