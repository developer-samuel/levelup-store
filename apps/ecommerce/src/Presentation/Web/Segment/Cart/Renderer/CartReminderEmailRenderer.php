<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\Cart\Renderer;

use Twig\Environment;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Web\Segment\Cart\Renderer\CartReminderEmailRendererContract;

final readonly class CartReminderEmailRenderer implements CartReminderEmailRendererContract
{
    public function __construct(
        private Environment $twig,
    ) {}

    public function renderCartReminderEmail(User $user, int $daysRemaining, string $cartUrl): string
    {
        return $this->twig->render('emails/cart/cart-reminder.html.twig', [
            'user'          => $user,
            'daysRemaining' => $daysRemaining,
            'cartUrl'       => $cartUrl,
        ]);
    }
}
