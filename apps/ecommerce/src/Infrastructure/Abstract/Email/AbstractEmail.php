<?php

declare(strict_types=1);

namespace App\Infrastructure\Abstract\Email;

use Symfony\{
    Component\Mailer\MailerInterface,
    Component\Mime\Email
};

abstract class AbstractEmail
{
    protected MailerInterface $mailer;
    protected string $fromEmail;

    public function __construct(
        MailerInterface $mailer,
        string $fromEmail,
    ) {
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
    }

    protected function createBaseEmail(string $toEmail, string $subject): Email
    {
        return (new Email())
            ->from($this->fromEmail)
            ->to($toEmail)
            ->subject($subject);
    }

    protected function sendEmail(Email $email): void
    {
        $this->mailer->send($email);
    }
}
