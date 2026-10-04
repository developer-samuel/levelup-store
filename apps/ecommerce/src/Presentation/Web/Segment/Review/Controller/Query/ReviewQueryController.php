<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\Review\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Review\Handler\Query\ReviewListQueryHandlerContract,
    Shared\Logging\AppLoggerContract,
    Web\Segment\Review\Renderer\ReviewRendererContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractQueryController
};

final class ReviewQueryController extends AbstractQueryController
{
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

    public function index(string $url): Response
    {
        $result = $this->reviewListQueryHandler->handle($url);
        if ($result === null) {
            return $this->redirectToRoute('products_index');
        }

        return $this->reviewRenderer->renderListForVariant(
            $result->list,
            $result->variant,
        );
    }
}
