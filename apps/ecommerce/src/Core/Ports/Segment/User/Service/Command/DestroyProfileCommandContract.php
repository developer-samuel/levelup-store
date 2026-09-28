<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Service\Command;

use App\Core\Domain\Segment\User\Entity\User;

interface DestroyProfileCommandContract
{
    /**
     * @param User $user
     *
     * @return void
    */
    public function destroyProfile(User $user): void;
}
