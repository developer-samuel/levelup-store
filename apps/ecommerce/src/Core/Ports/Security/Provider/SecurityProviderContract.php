<?php

declare(strict_types=1);

namespace App\Core\Ports\Security\Provider;

use App\Core\Domain\Segment\User\Entity\User;

interface SecurityProviderContract
{
    /**
     * @return User|null
     */
    public function getCurrentUser(): ?User;
}
