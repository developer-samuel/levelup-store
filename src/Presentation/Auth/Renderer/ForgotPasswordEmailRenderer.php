<?php

declare(strict_types=1);

namespace App\Presentation\Auth\Renderer;

use Twig\Environment;

use App\Core\Domain\{
    Auth\ValueObject\Email\ForgotPasswordEmailObject,
    Segment\User\Entity\User
};

use App\Core\Ports\Auth\Renderer\ForgotPasswordEmailRendererContract;

final readonly class ForgotPasswordEmailRenderer implements ForgotPasswordEmailRendererContract
{
    /**
     * @param Environment $twig
    */
    public function __construct(
        private Environment $twig,
    ) {}

    /**
     * @param string $resetUrl
     * @param User $user
     *
     * @return string
    */
    public function renderForgotPasswordEmail(string $resetUrl, User $user): string
    {
        $data = new ForgotPasswordEmailObject($resetUrl, $user);

        return $this->twig->render(
            'emails/password/forgot-password.html.twig',
            $data->toArray(),
        );
    }
}
