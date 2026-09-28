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

    /**
     * @param MailerInterface $mailer
     * @param string $fromEmail
    */
    public function __construct(
        MailerInterface $mailer,
        string $fromEmail,
    ) {
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
    }

    /**
     * @param string $toEmail
     * @param string $subject
     *
     * @return Email
    */
    protected function createBaseEmail(string $toEmail, string $subject): Email
    {
        return (new Email())
            ->from($this->fromEmail)
            ->to($toEmail)
            ->subject($subject);
    }

    /**
     * @param Email $email
     *
     * @return void
    */
    protected function sendEmail(Email $email): void
    {
        $this->mailer->send($email);
    }
}
