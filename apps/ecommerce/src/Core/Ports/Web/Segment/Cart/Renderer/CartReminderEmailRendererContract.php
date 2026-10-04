<?php

declare(strict_types=1);

namespace App\Core\Ports\Web\Segment\Cart\Renderer;

use App\Core\Domain\Segment\User\Entity\User;

interface CartReminderEmailRendererContract
{
    public function renderCartReminderEmail(User $user, int $daysRemaining, string $cartUrl): string;
}
