<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Service\Command;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Payload\ProfilePayload
};

interface ProfileCommandContract
{
    /**
     * @param User $user
     * @param ProfilePayload $payload
    */
    public function updateProfile(User $user, ProfilePayload $payload): void;
}
