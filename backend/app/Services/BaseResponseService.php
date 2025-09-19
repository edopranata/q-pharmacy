<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseResponseService
{
    /**
     * Return a success response
     *
     * @param  mixed  $data
     */
    public static function success($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return an error response
     *
     * @param  mixed  $errors
     */
    public static function error(string $message = 'Error', $errors = null, int $statusCode = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a paginated response
     */
    public static function paginated(LengthAwarePaginator $paginator, string $message = 'Data retrieved successfully', ?string $resourceClass = null): JsonResponse
    {
        $data = $paginator->items();

        // If resource class is provided, transform the data
        if ($resourceClass !== null) {
            $data = $resourceClass::collection($paginator->items())->resolve();
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ]
        ]);
    }

    /**
     * Return a collection response
     *
     * @param  mixed  $collection
     */
    public static function collection($collection, string $message = 'Data retrieved successfully', ?JsonResource $resource = null): JsonResponse
    {
        $data = $collection;

        // If resource is provided, transform the data
        if ($resource !== null) {
            $data = $resource::collection($collection)->resolve();
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Return a single resource response
     *
     * @param  mixed  $item
     */
    public static function resource($item, string $message = 'Data retrieved successfully', ?JsonResource $resource = null): JsonResponse
    {
        $data = $item;

        // If resource is provided, transform the data
        if ($resource !== null) {
            $data = (new $resource($item))->resolve();
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Return a validation error response
     *
     * @param  mixed  $errors
     */
    public static function validationError($errors, string $message = 'Validation failed'): JsonResponse
    {
        return self::error($message, $errors, 422);
    }

    /**
     * Return a not found response
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, null, 404);
    }

    /**
     * Return an unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, null, 401);
    }

    /**
     * Return a forbidden response
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::error($message, null, 403);
    }

    /**
     * Return a server error response
     */
    public static function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return self::error($message, null, 500);
    }

    /**
     * Return a created response
     *
     * @param  mixed  $data
     */
    public static function created($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * Return a no content response
     */
    public static function noContent(string $message = 'No content'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], 204);
    }
}
