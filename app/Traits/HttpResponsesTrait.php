<?php declare(strict_types = 1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait HttpResponsesTrait {
    // Methods
    /**
     * Method for successful responses
    */
    protected function success($data, string|array $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'Successful Request',
            'message' => $message ?? 'No message.',
            'data' => $data,
        ], $code);
    }

    /**
     * Method for failed responses
    */
    protected function error($data, string|array $message = null, int $code): JsonResponse
    {
        return response()->json([
            'status' => 'Failed Request',
            'message' => $message ?? 'No message.',
            'data' => $data,
        ], $code);
    }
}