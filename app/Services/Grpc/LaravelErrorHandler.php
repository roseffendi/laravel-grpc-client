<?php

namespace App\Services\Grpc;

use Google\Rpc\Status;
use Illuminate\Support\Facades\Log;
use GPBMetadata\Google\Rpc\ErrorDetails;
use App\Services\Grpc\Contracts\ErrorHandler;

class LaravelErrorHandler implements ErrorHandler
{
    /**
     * Handle grpc error
     * 
     * @param   object      $status
     * @param   array|int   $codeToSend
     * 
     * @return  mixed
     */
    public function handle($status, $codeToSend = 0)
    {
        $codeToSend = is_array($codeToSend) ? $codeToSend : [$codeToSend];

        if ($status->code != \Grpc\STATUS_OK) {
            ErrorDetails::initOnce();
            $details = $status->metadata['grpc-status-details-bin'][0] ?? null;

            $statusMessage = new Status();
            $statusMessage->mergeFromString($details);

            $errors = [];

            foreach ($statusMessage->getDetails() as $anyDetails) {
                $detail = $anyDetails->unpack();
                $detail->discardUnknownFields();

                $message = $detail->getDescription();
                $field = $detail->getField();

                $errors[$field] = [$message];
            }

            if (in_array($status->code, $codeToSend)) {
                $mappedCode = $this->mapGrpcToHtppCode($status->code);

                return response(['message' => $status->details, 'errors' => $errors], $mappedCode)->send();
            }

            Log::debug($status->code, [$status->details]);
        }
    }

    /**
     * Convert grpc status code to http status code.
     * 
     * @param   int  $code
     * 
     * @return  int
     */
    protected function mapGrpcToHtppCode($code)
    {
        $codes = [200, 499, 520, 422, 504, 404, 409, 403, 429, 400, 499, 400, 501, 500, 503, 408, 401];

        return $codes[$code];
    }
}
