<?php

declare(strict_types=1);

namespace App\Presentation\Abstract\Controller\Query;

use Symfony\{
    Bundle\FrameworkBundle\Controller\AbstractController,
    Component\HttpFoundation\Response
};

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\Shared\Responder\ExceptionResponder;

abstract class AbstractQueryController extends AbstractController
{
    /**
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    protected function __construct(
        protected readonly SecurityProviderContract $securityProvider,
        protected readonly ExceptionResponder $exceptionResponder,
        protected readonly AppLoggerContract $logger,
    ) {}

    /**
     * @param string $template
     * @param array<string, mixed> $data
     *
     * @return Response
     *
    */
    protected function renderPage(string $template, array $data = []): Response
    {
        $user = $this->securityProvider->getCurrentUser();

        try {
            $data['user'] = $user;

            return $this->render($template, $data);
        } catch (\Throwable $throwable) {
            $this->logger->logThrowable(
                'AbstractQueryController::renderPage',
                $throwable,
            );

            return $this->exceptionResponder->renderInternalServerError(
                $throwable,
                $user,
                'An unexpected error occurred.',
            );
        }
    }
}
