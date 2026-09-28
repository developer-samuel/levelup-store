<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Traits;

use App\Core\Domain\Segment\User\Entity\User;

/**
 * @property User $user
*/
trait UserTrait
{
    /**
     * @return User
    */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return self
    */
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
