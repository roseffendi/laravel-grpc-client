<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Grpc\Contracts\ErrorHandler;
use App\Services\Grpc\Contracts\ClientFactory;
use Protobuf\Identity\RegisterRequest;

class RegisterUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Passed data.
     * 
     * @var array
     */
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param   array   $data
     * 
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ClientFactory $grpcClientFactory, ErrorHandler $errorHandler)
    {
        $client = $grpcClientFactory->make(\Protobuf\Identity\AuthServiceClient::class);
        $request = $this->buildRequest();

        [$response, $status] = $client->Register($request)->wait();

        $errorHandler->handle($status, 3);
    }

    protected function buildRequest()
    {
        $request = new RegisterRequest;

        $request->setEmail($this->data['email']);
        $request->setName($this->data['name']);
        $request->setPassword($this->data['password']);
        $request->setPasswordConfirmation($this->data['password_confirmation']);

        return $request;
    }
}
