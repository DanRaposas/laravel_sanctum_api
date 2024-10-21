<?php declare(strict_types = 1);

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

trait ErrorHandlerTrait {
    use HttpResponsesTrait;

    // Method for returning responses after throwing an exception or error
    public function handleThrowable(\Throwable $error): JsonResponse
    {
        $errorCode = !$error->getCode() ? 500 : $error->getCode();
        $errorMessage = $error->getMessage() ?? 'Error';

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