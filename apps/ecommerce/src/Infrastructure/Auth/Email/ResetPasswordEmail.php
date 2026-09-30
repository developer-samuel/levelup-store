<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Email;

use Symfony\Component\Mailer\MailerInterface;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Auth\Renderer\ResetPasswordEmailRendererContract;

use App\Infrastructure\Abstract\Email\AbstractEmail;

final class ResetPasswordEmail extends AbstractEmail
{
    /**
     * @param ResetPasswordEmailRendererContract $renderer
     * @param MailerInterface $mailer
     * @param string $fromEmail
    */
    public function __construct(
        private readonly ResetPasswordEmailRendererContract $renderer,
        MailerInterface $mailer,
        string $fromEmail,
    ) {
        parent::__construct($mailer, $fromEmail);
    }

    /**
     * @param string $toEmail
     * @param User $user
     *
     * @return void
    */
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
