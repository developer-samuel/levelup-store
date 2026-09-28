<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;

final class JsonResponder
{
    /**
     * @param string $message
     *
     * @return JsonResponse
     */
    public static function notFound(
        string $message = 'Not found',
    ): JsonResponse {
        return new JsonResponse(
            ['error' => $message],
            404,
        );
    }
}
