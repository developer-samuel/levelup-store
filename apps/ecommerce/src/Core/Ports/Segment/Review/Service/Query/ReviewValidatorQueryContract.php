<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Service\Query;

use App\Core\Domain\Segment\User\Entity\User;

interface ReviewValidatorQueryContract
{
    /**
     * @param User $user
     * @param int $variantId
     * @param int $value
     *
     * @return void
    */
    public function validate(User $user, int $variantId, int $value): void;
}
