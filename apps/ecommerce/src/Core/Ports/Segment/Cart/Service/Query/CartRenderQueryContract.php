<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Service\Query;

use App\Core\Domain\Segment\User\Entity\User;

interface CartRenderQueryContract
{
    /**
     * @param User $user
     * @param string $message
     * @param bool $isError
     *
     * @return array<string, mixed>
    */
    public function buildCartResponse(User $user, string $message, bool $isError = false): array;
}
