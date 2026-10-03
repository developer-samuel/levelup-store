<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Service\Command;

use App\Core\Domain\{
    Auth\Payload\UpdateVerificationPayload,
    Segment\User\Entity\User
};

interface VerificationCommandContract
{
    public function createAndSaveTokenForUser(User $user): void;
    public function verifyUserByToken(UpdateVerificationPayload $payload): ?User;
}
