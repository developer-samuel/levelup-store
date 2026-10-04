<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Email;

use Symfony\Component\Mailer\MailerInterface;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Web\Auth\Renderer\VerificationEmailRendererContract;

use App\Infrastructure\Abstract\Email\AbstractEmail;

final class VerificationEmail extends AbstractEmail
{
    public function __construct(
        private readonly VerificationEmailRendererContract $renderer,
        MailerInterface $mailer,
        string $fromEmail,
    ) {
        parent::__construct($mailer, $fromEmail);
    }

    public function send(string $toEmail, string $resetUrl, User $user): void
    {
        $email = $this->createBaseEmail(
            $toEmail,
            'Verification Request',
        )
        ->html($this->renderer->renderVerificationEmail($resetUrl, $user));

        $this->sendEmail($email);
    }
}
