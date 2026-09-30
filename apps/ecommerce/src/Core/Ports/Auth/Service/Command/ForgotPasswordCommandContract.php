<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Service\Command;

use App\Core\Domain\Segment\User\Entity\User;

interface ForgotPasswordCommandContract
{
    /**
     * @param User $user
     *
     * @return void
    */
    public function createAndSaveTokenForUser(User $user): void;
}
