<?php

namespace App\Services\Grpc\Contracts;

interface ClientFactory
{
    /**
     * Make grpc client
     * 
     * @return  mixed
     */
    public function make(string $client);
}