<?php

declare(strict_types=1);

namespace App\Presentation\Api;

use Symfony\Component\HttpFoundation\JsonResponse;

final class HealthApiController
{
    public function health(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
}
