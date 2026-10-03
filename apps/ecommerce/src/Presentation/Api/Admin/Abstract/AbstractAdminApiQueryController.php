<?php

declare(strict_types=1);

namespace App\Presentation\Api\Admin\Abstract;

use Symfony\Component\HttpFoundation\JsonResponse;

use App\Presentation\{
    Shared\Responder\JsonResponder,
    Web\Abstract\Controller\Query\AbstractQueryController
};

abstract class AbstractAdminApiQueryController extends AbstractQueryController
{
    /** @param array<array<string, mixed>>|null $data */
    protected function respondWithList(?array $data, string $key): JsonResponse
    {
        if ($data === null || $data === []) {
            return JsonResponder::notFound();
        }

        return $this->json([$key => $data]);
    }
}
