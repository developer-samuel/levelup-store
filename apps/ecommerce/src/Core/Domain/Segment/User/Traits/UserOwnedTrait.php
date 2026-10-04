<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Traits;

use App\Core\Domain\Segment\User\Entity\User;

trait UserOwnedTrait
{
    public function isOwnedBy(User $user): bool
    {
        return $this->user->getId() === $user->getId();
    }
}
