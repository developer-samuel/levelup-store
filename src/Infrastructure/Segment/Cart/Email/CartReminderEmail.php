<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Cart\Email;

use Symfony\Component\Mailer\MailerInterface;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\{
    Segment\Cart\Email\CartReminderEmailContract,
    Segment\Cart\Renderer\Email\CartReminderEmailRendererContract
};

use App\Infrastructure\Abstract\Email\AbstractEmail;

class CartReminderEmail extends AbstractEmail implements CartReminderEmailContract
{
    /**
     * @param CartReminderEmailRendererContract $renderer
     * @param MailerInterface $mailer
     * @param string $fromEmail
    */
    public function __construct(
        private readonly CartReminderEmailRendererContract $renderer,
        MailerInterface $mailer,
        string $fromEmail,
    ) {
        parent::__construct($mailer, $fromEmail);
    }

    /**
     * @param User $user
     * @param int $daysRemaining
     * @param string $cartUrl
     *
     * @return void
    */
    public function send(User $user, int $daysRemaining, string $cartUrl): void
    {
        $email = $this->createBaseEmail(
            $user->getEmail(),
            'Did you forget something? 🛒',
        )
        ->html($this->renderer->renderCartReminderEmail($user, $daysRemaining, $cartUrl));

        $this->sendEmail($email);
    }
}
