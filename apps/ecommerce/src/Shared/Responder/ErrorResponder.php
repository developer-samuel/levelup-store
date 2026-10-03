<?php

declare(strict_types=1);

namespace App\Shared\Responder;

use Symfony\Component\HttpFoundation\Response;

use App\Shared\Renderer\ErrorRenderer;

final readonly class ErrorResponder
{
    public function __construct(
        private ErrorRenderer $errorRenderer,
    ) {}

    public function renderNotFound(string $message = 'Page not found'): Response
    {
        return new Response(
            $this->errorRenderer->renderNotFound($message),
            Response::HTTP_NOT_FOUND,
        );
    }

    public function renderUnauthorized(string $message = 'You must be logged in to view this page.'): Response
    {
        return new Response(
            $this->errorRenderer->renderUnauthorized($message),
            Response::HTTP_FORBIDDEN,
        );
    }

    public function renderInternalServerError(string $message = 'Internal Server Error'): Response
    {
        return new Response(
            $this->errorRenderer->renderInternalServerError($message),
            Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }
}
