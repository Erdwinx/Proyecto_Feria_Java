<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait ApiErrorHandler
{
    protected function logError(string $message, array $context = [], string $level = 'error'): void
    {
        Log::channel('api')->{$level}($message, $context);
    }

    protected function errorResponse(string $message, int $statusCode = 400, array $extra = [])
    {
        $this->logError($message, ['status' => $statusCode]);
        return response()->json(array_merge(['message' => $message], $extra), $statusCode);
    }

    protected function successResponse(array $data = [], int $statusCode = 200)
    {
        $this->logError('Success: ' . json_encode($data), [], 'info');
        return response()->json($data, $statusCode);
    }
}
