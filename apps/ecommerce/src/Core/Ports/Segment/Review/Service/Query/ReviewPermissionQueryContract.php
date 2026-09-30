<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Service\Query;

use App\Core\Domain\Segment\User\Entity\User;

interface ReviewPermissionQueryContract
{
    /**
     * @param User $user
     * @param int $variantId
     *
     * @return bool
    */
    public function canUserCreateReview(User $user, int $variantId): bool;
}
