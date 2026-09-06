<?php

declare(strict_types=1);

namespace App\Presentation\Auth\Renderer;

use Twig\Environment;

use App\Core\Domain\{
    Auth\ValueObject\Email\VerificationEmailObject,
    Segment\User\Entity\User
};

use App\Core\Ports\Auth\Renderer\VerificationEmailRendererContract;

final readonly class VerificationEmailRenderer implements VerificationEmailRendererContract
{
    /**
     * @param Environment $twig
    */
    public function __construct(
        private Environment $twig,
    ) {}

    /**
     * @param string $verificationUrl
     * @param User $user
     *
     * @return string
    */
    public function renderVerificationEmail(string $verificationUrl, User $user): string
    {
        $data = new VerificationEmailObject($verificationUrl, $user);

        return $this->twig->render(
            'emails/auth/verification.html.twig',
            $data->toArray(),
        );
    }
}
