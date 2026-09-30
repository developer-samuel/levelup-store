<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Cart\Renderer;

use Twig\Environment;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Segment\Cart\Renderer\CartReminderEmailRendererContract;

final readonly class CartReminderEmailRenderer implements CartReminderEmailRendererContract
{
    /**
     * @param Environment $twig
    */
    public function __construct(
        private Environment $twig,
    ) {}

    /**
     * @param User $user
     * @param int $daysRemaining
     * @param string $cartUrl
     *
     * @return string
    */
    public function renderCartReminderEmail(User $user, int $daysRemaining, string $cartUrl): string
    {
        return $this->twig->render('emails/cart/cart-reminder.html.twig', [
            'user'          => $user,
            'daysRemaining' => $daysRemaining,
            'cartUrl'       => $cartUrl,
        ]);
    }
}
