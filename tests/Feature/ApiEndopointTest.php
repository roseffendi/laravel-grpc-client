<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Protobuf\Identity\RegisterResponse;
use Protobuf\Identity\UserResponse;
use stdClass;
use Tests\TestCase;

class ApiEndopointTest extends TestCase
{
    public function testItCanRegister()
    {
        $registerResponse = new RegisterResponse;
        $clientFactory = $this->mock(\App\Services\Grpc\Contracts\ClientFactory::class);
        $authServiceClient = $this->mock(\Protobuf\Identity\AuthServiceClient::class);
        $baseStub = $this->mock(\Grpc\BaseStub::class); // It's a dummy class

        $status = new stdClass;

        $status->code = 0;
        $status->descriptions = '';

        $baseStub->shouldReceive('wait')->andReturn([
            $registerResponse,
            $status
        ]);

        $authServiceClient->shouldReceive('Register')->andReturn($baseStub);
        $clientFactory->shouldReceive('make')->with('Protobuf\\Identity\\AuthServiceClient')->andReturn($authServiceClient);

        $this->app->bind(\App\Services\Grpc\Contracts\ClientFactory::class, function() use ($clientFactory){
            return $clientFactory;
        });
        
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->json('POST', '/api/v1/register', [
            'email' => 'email@email.com',
            'name' => 'Full name',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        $response->assertStatus(200);
    }
}
