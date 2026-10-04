<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Traits;

use App\Core\Domain\Segment\User\Entity\User;

trait UserTrait
{
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
