<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Api\Abstract;

use Symfony\Component\HttpFoundation\JsonResponse;

use App\Presentation\{
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\JsonResponder
};

abstract class AbstractAdminApiQueryController extends AbstractQueryController
{
    /**
     * @param array<array<string, mixed>>|null $data
     * @param string $key
     *
     * @return JsonResponse
    */
    protected function respondWithList(?array $data, string $key): JsonResponse
    {
        if ($data === null || $data === []) {
            return JsonResponder::notFound();
        }

        return $this->json([$key => $data]);
    }
}
