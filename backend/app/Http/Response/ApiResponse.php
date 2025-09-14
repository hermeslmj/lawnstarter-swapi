<?php

namespace App\Http\Response;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Create success response
     *
     * @param mixed $data
     * @param int $code
     * @return JsonResponse
     */
    public static function success(mixed $data, int $code = 200): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'content' => $data
        ], $code);
    }

    /**
     * Create error response
     *
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return JsonResponse
     */
    public static function error(string $message, int $code = 400, mixed $errors = null): JsonResponse
    {
        $response = [
            'code' => $code,
            'content' => [
                'message' => $message
            ]
        ];

        if ($errors !== null) {
            $response['content']['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Create not found response
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, 404);
    }

    /**
     * Create validation error response
     *
     * @param array $errors
     * @return JsonResponse
     */
    public static function validationError(array $errors): JsonResponse
    {
        return self::error('Validation failed', 422, $errors);
    }
}
