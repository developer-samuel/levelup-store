<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Email;

use Symfony\Component\Mailer\MailerInterface;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Auth\Renderer\ForgotPasswordEmailRendererContract;

use App\Infrastructure\Abstract\Email\AbstractEmail;

class ForgotPasswordEmail extends AbstractEmail
{
    /**
     * @param ForgotPasswordEmailRendererContract $renderer
     * @param MailerInterface $mailer
     * @param string $fromEmail
    */
    public function __construct(
        private readonly ForgotPasswordEmailRendererContract $renderer,
        MailerInterface $mailer,
        string $fromEmail,
    ) {
        parent::__construct($mailer, $fromEmail);
    }

    /**
     * @param string $toEmail
     * @param string $resetUrl
     * @param User $user
     *
     * @return void
    */
    public function send(string $toEmail, string $resetUrl, User $user): void
    {
        $email = $this->createBaseEmail(
            $toEmail,
            'Password Reset Request',
        )
        ->html($this->renderer->renderForgotPasswordEmail($resetUrl, $user));

        $this->sendEmail($email);
    }
}
