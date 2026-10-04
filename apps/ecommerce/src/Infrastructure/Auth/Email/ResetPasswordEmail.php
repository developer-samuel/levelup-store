<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Email;

use Symfony\Component\Mailer\MailerInterface;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Web\Auth\Renderer\ResetPasswordEmailRendererContract;

use App\Infrastructure\Abstract\Email\AbstractEmail;

final class ResetPasswordEmail extends AbstractEmail
{
    public function __construct(
        private readonly ResetPasswordEmailRendererContract $renderer,
        MailerInterface $mailer,
        string $fromEmail,
    ) {
        parent::__construct($mailer, $fromEmail);
    }

    public function send(string $toEmail, User $user): void
    {
        $email = $this->createBaseEmail(
            $toEmail,
            'Reset Password Confirmation',
        )
        ->html($this->renderer->renderResetPasswordEmail($user));

        $this->sendEmail($email);
    }
}
