<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;

use App\Presentation\Shared\Utils\MessageResolver;

use Kit\Utils\Shared\Sanitizer\DataSanitizer;

final class ResultResponder
{
    /**
     * @param array<string, mixed> $result
     *
     * @return JsonResponse
    */
    public static function success(array $result): JsonResponse
    {
        return self::renderSuccessInternal($result, false);
    }

    /**
     * @param array<string, mixed> $result
     *
     * @return JsonResponse
    */
    public static function successWithRedirect(array $result): JsonResponse
    {
        return self::renderSuccessInternal($result, true);
    }

    /**
     * @param array<string, mixed> $result
     * @param bool $redirect
     *
     * @return JsonResponse
    */
    private static function renderSuccessInternal(array $result, bool $redirect): JsonResponse
    {
        $errorResponse = self::renderError($result);
        if ($errorResponse !== null) {
            return $errorResponse;
        }

        $message = MessageResolver::resolve($result);

        return $redirect
            ? HttpResponder::successWithRedirect($result, $message)
            : HttpResponder::success([], $message);
    }

    /**
     * @param array<string, mixed> $result
     *
     * @return JsonResponse|null
    */
    private static function renderError(array $result): ?JsonResponse
    {
        if (isset($result['status']) && $result['status'] === 'error') {
            $statusCode = self::resolveStatusCode($result);
            return new JsonResponse($result, $statusCode);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $result
     *
     * @return int
    */
    private static function resolveStatusCode(array $result): int
    {
        $default = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;

        if (isset($result['code'])) {
            $code = DataSanitizer::sanitizeInt($result['code']);
            if ($code !== null) {
                return $code;
            }
        }

        return $default;
    }
}
