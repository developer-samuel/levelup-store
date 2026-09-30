<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Service\Command;

use App\Core\Domain\{
    Auth\Payload\UpdateVerificationPayload,
    Segment\User\Entity\User
};

interface VerificationCommandContract
{
    /**
     * @param User $user
     *
     * @return void
    */
    public function createAndSaveTokenForUser(User $user): void;

    /**
     * @param UpdateVerificationPayload $payload
     *
     * @return User|null
    */
    public function verifyUserByToken(UpdateVerificationPayload $payload): ?User;
}
