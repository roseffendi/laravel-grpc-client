<?php

namespace Tests\Unit;

use App\Services\Grpc\ConfigurableClientFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConfigugrableClientFactoryTest extends TestCase
{
    /** @var \Mockery\MockInterface|\Illuminate\Contracts\Config\Repository */
    protected $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = $this->mock(\Illuminate\Contracts\Config\Repository::class);
    }

    public function testItIaAConfigRepository()
    {
        $configurableClientRepository = new ConfigurableClientFactory($this->config);
        
        $this->assertTrue($configurableClientRepository instanceof \App\Services\Grpc\Contracts\ClientFactory);
    }

    public function testItCanCreateInsecureConnection()
    {
        $this->config->shouldReceive('get')
                     ->with('grpc.services.Protobuf\\Identity\\AuthServiceClient')
                     ->once()
                     ->andReturn([
                         'host' => 'test-host',
                         'authentication' => 'insecure'
                     ]);

        $configurableClientRepository = new ConfigurableClientFactory($this->config);

        $client = $configurableClientRepository->make('Protobuf\\Identity\\AuthServiceClient');

        $this->assertSame($client->getTarget(), 'test-host');
    }

    public function testItCanCreateTlsConnection()
    {
        $this->config->shouldReceive('get')
                     ->with('grpc.services.Protobuf\\Identity\\AuthServiceClient')
                     ->once()
                     ->andReturn([
                         'host' => 'test-host',
                         'authentication' => 'tls',
                         'cert' => './tests/test.crt'
                     ]);

        $configurableClientRepository = new ConfigurableClientFactory($this->config);

        $client = $configurableClientRepository->make('Protobuf\\Identity\\AuthServiceClient');

        $this->assertSame($client->getTarget(), 'test-host');
    }
}
