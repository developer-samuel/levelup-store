<?php

declare(strict_types=1);

namespace App\Presentation\Abstract\Controller\Command;

use Symfony\{
    Bundle\FrameworkBundle\Controller\AbstractController,
    Component\HttpFoundation\JsonResponse
};

use App\Core\Ports\Shared\Logging\AppLoggerContract;

use App\Presentation\Shared\Responder\HttpResponder;

abstract class AbstractCommandController extends AbstractController
{
    /**
     * @param AppLoggerContract $logger
    */
    protected function __construct(
        protected readonly AppLoggerContract $logger,
    ) {}

    /**
     * @param callable(): JsonResponse $callback
     *
     * @return JsonResponse
    */
    protected function handleCommand(callable $callback): JsonResponse
    {
        try {
            return $callback();
        } catch (\Throwable $throwable) {
            $this->logger->logThrowable(
                'AbstractCommandController::handleCommand',
                $throwable,
            );

            return HttpResponder::internalServerError();
        }
    }
}
