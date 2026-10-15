<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Notifier;

use App\Core\Domain\Segment\User\Entity\User;

interface ReviewNotifierContract
{
    public function notifySubmitted(int $variantId, User $user): void;
    public function notifyDestroyed(int $reviewId, User $user): void;
}
