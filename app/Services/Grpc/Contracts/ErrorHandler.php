<?php

namespace App\Services\Grpc\Contracts;

interface ErrorHandler
{
    /**
     * Handle grpc error
     * 
     * @param   object           $status
     * @param   array|int|null   $codeToSend
     * 
     * @return  mixed
     */
    public function handle($status, $codeToSend = null);
}