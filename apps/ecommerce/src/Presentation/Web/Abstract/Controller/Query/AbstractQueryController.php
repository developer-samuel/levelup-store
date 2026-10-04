<?php

declare(strict_types=1);

namespace App\Presentation\Web\Abstract\Controller\Query;

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
    protected function __construct(
        protected readonly SecurityProviderContract $securityProvider,
        protected readonly ExceptionResponder $exceptionResponder,
        protected readonly AppLoggerContract $logger,
    ) {}

    /** @param array<string, mixed> $data */
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
