<?php

declare(strict_types=1);

namespace App\Shared\Responder;

use Symfony\Component\HttpFoundation\Response;

use App\Shared\Renderer\ErrorRenderer;

final readonly class ErrorResponder
{
    /**
     * @param ErrorRenderer $errorRenderer
    */
    public function __construct(
        private ErrorRenderer $errorRenderer,
    ) {}

    /**
     * @param string $message
     *
     * @return Response
    */
    public function renderNotFound(string $message = 'Page not found'): Response
    {
        return new Response(
            $this->errorRenderer->renderNotFound($message),
            Response::HTTP_NOT_FOUND,
        );
    }

    /**
     * @param string $message
     *
     * @return Response
    */
    public function renderUnauthorized(string $message = 'You must be logged in to view this page.'): Response
    {
        return new Response(
            $this->errorRenderer->renderUnauthorized($message),
            Response::HTTP_FORBIDDEN,
        );
    }

    /**
     * @param string $message
     *
     * @return Response
    */
    public function renderInternalServerError(string $message = 'Internal Server Error'): Response
    {
        return new Response(
            $this->errorRenderer->renderInternalServerError($message),
            Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }
}
