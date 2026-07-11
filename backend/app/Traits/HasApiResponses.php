<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait HasApiResponses
{
    protected function success(
        mixed   $data = null,
        ?string $message = null,
        mixed   $meta = null,
        int     $code = Response::HTTP_OK,
    ): JsonResponse {
        $responseData = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $responseData['data'] = $data;
        }

        if (!is_null($meta)) {
            $responseData['meta'] = $meta;
        }

        return response()->json($responseData, $code);
    }

    protected function created(
        mixed   $data = null,
        ?string $message = null,
    ): JsonResponse {
        return $this->success($data, $message, Response::HTTP_CREATED);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param array<string, mixed> $errors
     */
    protected function error(
        string $message = 'Error',
        int    $code = Response::HTTP_BAD_REQUEST,
        array  $errors = [],
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== []) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, Response::HTTP_NOT_FOUND);
    }

    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, Response::HTTP_UNAUTHORIZED);
    }

    protected function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->error($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * @param array<string, mixed> $errors
     */
    protected function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->error($message, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }
}
