<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Message;

final readonly class UserIndexMessage
{
    /**
     * @param int $userId
    */
    public function __construct(
        public int $userId,
    ) {}
}
