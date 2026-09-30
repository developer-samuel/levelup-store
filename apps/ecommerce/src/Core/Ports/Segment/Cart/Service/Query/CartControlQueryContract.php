<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Service\Query;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\User\Entity\User
};

interface CartControlQueryContract
{
    /**
     * @param User $user
     *
     * @return Cart|null
    */
    public function getUserCart(User $user): ?Cart;
}
