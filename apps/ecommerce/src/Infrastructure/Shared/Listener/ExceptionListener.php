<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Listener;

use Symfony\{
    Component\EventDispatcher\Attribute\AsEventListener,
    Component\HttpFoundation\Response,
    Component\HttpKernel\Event\ExceptionEvent,
    Component\HttpKernel\Exception\HttpExceptionInterface,
    Component\HttpKernel\Exception\NotFoundHttpException,
    Component\HttpKernel\KernelEvents
};

use App\Core\Ports\Shared\Logging\AppLoggerContract;

use App\Shared\Renderer\ErrorRenderer;

#[AsEventListener(
    event: KernelEvents::EXCEPTION,
    method: 'onKernelException',
)]
final readonly class ExceptionListener
{
    /**
     * @param ErrorRenderer $errorRenderer
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private ErrorRenderer $errorRenderer,
        private AppLoggerContract $logger,
    ) {}

    /**
     * @param ExceptionEvent $event
     *
     * @return void
    */
    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        $this->logException($throwable);

        $response = $this->getExceptionResponse($throwable);

        $event->setResponse($response);
    }

    /**
     * @param \Throwable $throwable
     *
     * @return void
    */
    private function logException(\Throwable $throwable): void
    {
        $message = sprintf(
            'Exception occurred: %s (%s) at %s:%d',
            $throwable->getMessage(),
            get_class($throwable),
            $throwable->getFile(),
            $throwable->getLine(),
        );

        $this->logger->alert($message, $throwable);
    }

    /**
     * @param \Throwable $throwable
     *
     * @return Response
    */
    private function getExceptionResponse(\Throwable $throwable): Response
    {
        return match (true) {
            $throwable instanceof NotFoundHttpException  => $this->createNotFoundResponse(),
            $throwable instanceof HttpExceptionInterface => $this->createInternalServerErrorResponse(),
            default                                      => $this->createInternalServerErrorResponse(),
        };
    }

    /**
     * @return Response
    */
    private function createNotFoundResponse(): Response
    {
        $html = $this->errorRenderer->renderNotFound();

        return $this->createHtml($html, Response::HTTP_NOT_FOUND);
    }

    /**
     * @return Response
    */
    private function createInternalServerErrorResponse(): Response
    {
        $html = $this->errorRenderer->renderInternalServerError();

        return $this->createHtml(
            $html,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }

    /**
     * @param string $html
     * @param int $status
     *
     * @return Response
    */
    private function createHtml(string $html, int $status): Response
    {
        return new Response(
            $html,
            $status,
            ['Content-Type' => 'text/html'],
        );
    }
}
