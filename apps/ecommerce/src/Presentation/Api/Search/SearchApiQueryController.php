<?php

declare(strict_types=1);

namespace App\Presentation\Api\Search;

use Symfony\{
    Bundle\FrameworkBundle\Controller\AbstractController,
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request
};

use OpenApi\Attributes as OA;

use App\Core\Ports\{
    Search\Handler\Query\SearchRenderQueryHandlerContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\Shared\Responder\ExceptionResponder;

final class SearchApiQueryController extends AbstractController
{
    /**
     * @param SearchRenderQueryHandlerContract $searchRenderQueryHandler
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly SearchRenderQueryHandlerContract $searchRenderQueryHandler,
        private readonly ExceptionResponder $exceptionResponder,
        private readonly AppLoggerContract $logger,
    ) {}

    #[OA\Get(
        path: '/api/search',
        summary: 'Search products',
        tags: ['Search'],
        security: [],
        parameters: [
            new OA\Parameter(name: 'query', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Search results'),
            new OA\Response(response: 500, description: 'Internal server error'),
        ],
    )]
   public function search(Request $request): JsonResponse
    {
        $query = $request->query->getString('query');

        try {
            $result = $this->searchRenderQueryHandler->handle($query);

            return new JsonResponse($result);
        } catch (\Throwable $throwable) {
            $this->logger->logThrowable(
                'SearchApiQueryController::search',
                $throwable,
            );

            return $this->exceptionResponder->renderInternalServerErrorJson($throwable);
        }
    }
}
