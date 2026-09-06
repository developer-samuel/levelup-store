<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Renderer;

use App\Core\Domain\Segment\User\Entity\User;

interface CartReminderEmailRendererContract
{
    /**
     * @param User $user
     * @param int $daysRemaining
     * @param string $cartUrl
     *
     * @return string
    */
    public function renderCartReminderEmail(User $user, int $daysRemaining, string $cartUrl): string;
}
