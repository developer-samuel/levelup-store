<?php

declare(strict_types=1);

namespace App\Shared\Renderer;

use Twig\Environment;

final readonly class ErrorRenderer
{
    /**
     * @param Environment $twig
    */
    public function __construct(
        private Environment $twig,
    ) {}

    /**
     * @param string $message
     *
     * @return string
    */
    public function renderUnauthorized(string $message = 'You must be logged in to view this page.'): string
    {
        return $this->render(
            'errors/403.html.twig',
            $message,
        );
    }

    /**
     * @param string $message
     *
     * @return string
    */
    public function renderNotFound(string $message = 'Page Not Found'): string
    {
        return $this->render(
            'errors/404.html.twig',
            $message,
        );
    }

    /**
     * @param string $message
     *
     * @return string
    */
    public function renderInternalServerError(string $message = 'Internal Server Error'): string
    {
        return $this->render(
            'errors/500.html.twig',
            $message,
        );
    }

    /**
     * @param string $template
     * @param string $message
     *
     * @return string
    */
    private function render(string $template, string $message): string
    {
        return $this->twig->render($template, [
            'message' => $message,
        ]);
    }
}
