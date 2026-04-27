<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Unified API response formatter.
 *
 * Every client auth endpoint MUST use these methods instead of raw response()->json().
 * This guarantees a consistent envelope across all endpoints.
 */
final class ApiResponse
{
    /**
     * Success response.
     *
     * @param  mixed       $data       Payload (object, array, or null)
     * @param  string      $code       ErrorCodes constant
     * @param  string|null $message    Optional fallback message for debugging
     * @param  int         $statusCode HTTP status code (200, 201, etc.)
     */
    public static function success(
        mixed $data = null,
        string $code = 'SUCCESS',
        ?string $message = null,
        int $statusCode = 200
    ): JsonResponse {
        $body = [
            'success'     => true,
            'status_code' => $statusCode,
            'code'        => $code,
        ];

        if ($message !== null) {
            $body['message'] = $message;
        }

        $body['data'] = $data;

        return response()->json($body, $statusCode);
    }

    /**
     * Error response.
     *
     * @param  string      $code       ErrorCodes constant
     * @param  string|null $message    Optional fallback message
     * @param  int         $statusCode HTTP status code (400, 401, 422, etc.)
     * @param  array|null  $errors     Validation error bag (field => [messages])
     */
    public static function error(
        string $code,
        ?string $message = null,
        int $statusCode = 400,
        ?array $errors = null
    ): JsonResponse {
        $body = [
            'success'     => false,
            'status_code' => $statusCode,
            'code'        => $code,
        ];

        if ($message !== null) {
            $body['message'] = $message;
        }

        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        return response()->json($body, $statusCode);
    }

    /**
     * Validation error response (422).
     * Wraps Form Request validation failures with the standard envelope.
     */
    public static function validationError(array $errors, string $code = 'AUTH_VALIDATION_ERROR'): JsonResponse
    {
        return self::error(
            code: $code,
            message: 'The given data was invalid.',
            statusCode: 422,
            errors: $errors,
        );
    }
}
