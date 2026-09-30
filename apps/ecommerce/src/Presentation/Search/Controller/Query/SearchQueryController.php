<?php

declare(strict_types=1);

namespace App\Presentation\Search\Controller\Query;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\HttpFoundation\Response
};

use App\Core\Ports\{
    Search\Handler\Query\SearchPageQueryHandlerContract,
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\ExceptionResponder
};

final class SearchQueryController extends AbstractQueryController
{
    /**
     * @param SearchPageQueryHandlerContract $searchPageQueryHandler
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly SearchPageQueryHandlerContract $searchPageQueryHandler,
        SecurityProviderContract $securityProvider,
        ExceptionResponder $exceptionResponder,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $securityProvider,
            $exceptionResponder,
            $logger,
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
    */
    public function index(Request $request): Response
    {
        $query = $request->query->getString('query');

        try {
            $html = $this->searchPageQueryHandler->handle($query);

            return new Response($html);
        } catch (\Throwable $throwable) {
            $this->logger->logThrowable(
                'SearchQueryController::index',
                $throwable,
            );

            return $this->exceptionResponder->renderInternalServerError($throwable);
        }
    }
}
