<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Service\Command;

use App\Core\Domain\Segment\User\Entity\User;

interface ReviewRatingCommandContract
{
    public function toggle(int $id, User $user, ?string $type): bool;
}
