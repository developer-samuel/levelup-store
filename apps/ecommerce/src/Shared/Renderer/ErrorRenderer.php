<?php

declare(strict_types=1);

namespace App\Shared\Renderer;

use Twig\Environment;

final readonly class ErrorRenderer
{
    public function __construct(
        private Environment $twig,
    ) {}

    public function renderUnauthorized(string $message = 'You must be logged in to view this page.'): string
    {
        return $this->render(
            'errors/403.html.twig',
            $message,
        );
    }

    public function renderNotFound(string $message = 'Page Not Found'): string
    {
        return $this->render(
            'errors/404.html.twig',
            $message,
        );
    }

    public function renderInternalServerError(string $message = 'Internal Server Error'): string
    {
        return $this->render(
            'errors/500.html.twig',
            $message,
        );
    }

    private function render(string $template, string $message): string
    {
        return $this->twig->render($template, [
            'message' => $message,
        ]);
    }
}
