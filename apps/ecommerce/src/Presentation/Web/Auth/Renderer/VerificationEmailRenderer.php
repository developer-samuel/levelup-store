<?php

declare(strict_types=1);

namespace App\Presentation\Web\Auth\Renderer;

use Twig\Environment;

use App\Core\Domain\{
    Auth\ValueObject\Email\VerificationEmailObject,
    Segment\User\Entity\User
};

use App\Core\Ports\Web\Auth\Renderer\VerificationEmailRendererContract;

final readonly class VerificationEmailRenderer implements VerificationEmailRendererContract
{
    public function __construct(
        private Environment $twig,
    ) {}

    public function renderVerificationEmail(string $verificationUrl, User $user): string
    {
        $data = new VerificationEmailObject($verificationUrl, $user);

        return $this->twig->render(
            'emails/auth/verification.html.twig',
            $data->toArray(),
        );
    }
}
