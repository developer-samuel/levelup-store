<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Responder;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Response
};

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Shared\Logging\AppLoggerContract;

final readonly class ExceptionResponder
{
    /**
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private AppLoggerContract $logger,
    ) {}

    /**
     * @param \Throwable $throwable
     * @param User|null $user
     * @param string|null $message
     *
     * @return Response
    */
    public function renderInternalServerError(
        \Throwable $throwable,
        ?User $user = null,
        ?string $message = null,
    ): Response {
        $this->logCritical($throwable, $user);

        return new Response(
            $message ?? 'An error occurred while processing your request.',
            Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }

    /**
     * @param \Throwable $throwable
     * @param User|null $user
     * @param string|null $message
     *
     * @return JsonResponse
    */
    public function renderInternalServerErrorJson(
        \Throwable $throwable,
        ?User $user = null,
        ?string $message = 'An unexpected error occurred.',
    ): JsonResponse {
        $this->logCritical($throwable, $user);

        return new JsonResponse(
            [
                'status'  => 'error',
                'message' => $message,
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }

    /**
     * @param \Throwable $throwable
     * @param User|null $user
     *
     * @return void
    */
    private function logCritical(\Throwable $throwable, ?User $user = null): void
    {
        $normalizedException = $this->normalizeException($throwable);

        $this->logger->critical(
            $normalizedException->getMessage(),
            $normalizedException,
            $user,
        );
    }

    /**
     * @param \Throwable $throwable
     *
     * @return \Exception
    */
    private function normalizeException(\Throwable $throwable): \Exception
    {
        return $throwable instanceof \Exception
            ? $throwable
            : new \Exception(
                $throwable->getMessage(),
                $throwable->getCode(),
                $throwable,
            );
    }
}
