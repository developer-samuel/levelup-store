<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Responder;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Response
};

use App\Presentation\{
    Shared\Utils\MessageResolver,
    Shared\Utils\ResponseFormatter
};

final class HttpResponder
{
    /**
     * @param array<string, mixed> $data
     * @param string|null $message
     *
     * @return JsonResponse
    */
    public static function success(array $data = [], ?string $message = ''): JsonResponse
    {
        return self::createResponse(
            ResponseFormatter::success($message, $data),
            Response::HTTP_OK,
        );
    }

    /**
     * @param array<string, mixed> $result
     * @param string|null $message
     *
     * @return JsonResponse
    */
    public static function successWithRedirect(array $result, ?string $message = ''): JsonResponse
    {
        $message = MessageResolver::resolve($result, $message);

        return self::success(
            ['redirect' => $result['redirect'] ?? null],
            $message ?? null,
        );
    }

    /**
     * @param array<string, mixed> $errors
     * @param string|null $message
     *
     * @return JsonResponse
    */
    public static function unprocessableEntity(array $errors = [], ?string $message = ''): JsonResponse
    {
        return self::createResponse(
            ResponseFormatter::errors($message, $errors),
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /**
     * @param string|null $message
     *
     * @return JsonResponse
    */
    public static function unauthorized(?string $message = 'User is not authenticated'): JsonResponse
    {
        return self::createResponse(
            ResponseFormatter::errors($message),
            Response::HTTP_UNAUTHORIZED,
        );
    }

    /**
     * @param string|null $message
     *
     * @return JsonResponse
    */
    public static function accessDenied(?string $message = 'Access denied'): JsonResponse
    {
        return self::createResponse(
            ResponseFormatter::errors($message),
            Response::HTTP_FORBIDDEN,
        );
    }

    /**
     * @param string|null $message
     *
     * @return JsonResponse
    */
    public static function internalServerError(
        ?string $message = 'An error occurred while processing your request.',
    ): JsonResponse {
        return self::createResponse(
            ResponseFormatter::errors($message),
            Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }

    /**
     * @param array<string, mixed> $data
     * @param int $status
     *
     * @return JsonResponse
    */
    private static function createResponse(array $data, int $status): JsonResponse
    {
        return new JsonResponse($data, $status);
    }
}
