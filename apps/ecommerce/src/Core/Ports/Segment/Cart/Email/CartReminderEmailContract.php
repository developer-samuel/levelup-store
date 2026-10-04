<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Email;

use App\Core\Domain\Segment\User\Entity\User;

interface CartReminderEmailContract
{
    public function send(User $user, int $daysRemaining, string $cartUrl): void;
}
