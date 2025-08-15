<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses 
{
    protected function ok(string $message, array $data = []): JsonResponse
    {
        return $this->success($message, $data, 200);
    }

    protected function success(string $message, array $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }

    protected function error(string|array $errors = [], int $statusCode = null): JsonResponse
    {
        if (is_string($errors)) {
            return response()->json([
                'message' => $errors,
                'status' => $statusCode
            ], $statusCode);
        }

        return response()->json([
            'errors' => $errors,
            'status' => $statusCode ?? 400
        ], $statusCode ?? 400);
    }

    protected function notAuthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status' => 401
        ], 401);
    }

    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status' => 404
        ], 404);
    }

    protected function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
            'status' => 422
        ], 422);
    }

    protected function created(string $message, array $data = []): JsonResponse
    {
        return $this->success($message, $data, 201);
    }
}
