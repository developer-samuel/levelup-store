<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Review\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Review\Handler\Query\ReviewListQueryHandlerContract,
    Segment\Review\Renderer\ReviewRendererContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\ExceptionResponder
};

final class ReviewQueryController extends AbstractQueryController
{
    /**
     * @param ReviewListQueryHandlerContract $reviewListQueryHandler
     * @param ReviewRendererContract $reviewRenderer
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly ReviewListQueryHandlerContract $reviewListQueryHandler,
        private readonly ReviewRendererContract $reviewRenderer,
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
     * @param string $url
     *
     * @return Response
    */
    public function index(string $url): Response
    {
        $result = $this->reviewListQueryHandler->handle($url);
        if (!$result) {
            return $this->redirectToRoute('products_index');
        }

        return $this->reviewRenderer->renderListForVariant(
            $result->list,
            $result->variant,
        );
    }
}
