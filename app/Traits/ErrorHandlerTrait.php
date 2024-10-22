<?php declare(strict_types = 1);

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

trait ErrorHandlerTrait {
    // Traits
    use HttpResponsesTrait;

    /**
     * Method for returning responses after throwing an exception or error
    */
    protected function handleThrowable(\Throwable $error): JsonResponse
    {
        $errorMessage = $error->getMessage() ?? 'Error';

        // Executed if the error code is invalid or not an int
        if($error->getCode() < 400 || !is_int($error->getCode())) {
            $errorCode = 500;
        } else {
            $errorCode = $error->getCode();
        }

        // Executed if the thrown exception is authorization-related
        if($error instanceof AuthorizationException) {
            return $this->error([
                'error_code' => 403,
                'message' => $errorMessage,
            ], 'Unauthorized', 403);
        }

        // Executed otherwise
        return $this->error([
            'error_code' => $errorCode,
            'message' => $errorMessage,
        ], 'Something went wrong.', $errorCode);
    }
}