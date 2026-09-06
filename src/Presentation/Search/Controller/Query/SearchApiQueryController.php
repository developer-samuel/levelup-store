<?php

declare(strict_types=1);

namespace App\Presentation\Search\Controller\Query;

use Symfony\{
    Bundle\FrameworkBundle\Controller\AbstractController,
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request
};

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

    /**
     * @param Request $request
     *
     * @return JsonResponse
    */
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
