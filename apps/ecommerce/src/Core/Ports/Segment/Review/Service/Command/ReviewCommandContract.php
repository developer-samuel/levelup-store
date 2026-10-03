<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Service\Command;

use App\Core\Domain\{
    Segment\Review\Payload\ReviewCreatePayload,
    Segment\User\Entity\User
};

interface ReviewCommandContract
{
    public function add(ReviewCreatePayload $payload, User $user): void;
    public function remove(int $id, User $user): void;
}
