<?php

declare(strict_types=1);

namespace App\Presentation\Web\Auth\Renderer;

use Twig\Environment;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Web\Auth\Renderer\ResetPasswordEmailRendererContract;

final readonly class ResetPasswordEmailRenderer implements ResetPasswordEmailRendererContract
{
    public function __construct(
        private Environment $twig,
    ) {}

    public function renderResetPasswordEmail(User $user): string
    {
        return $this->twig->render(
            'emails/password/reset-password.html.twig',
            ['user' => $user],
        );
    }
}
