<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Handler\Query;

use App\Core\Domain\{
    Segment\Order\ValueObject\OrderDetailObject,
    Segment\User\Entity\User
};

interface GetOrderDetailQueryHandlerContract
{
    /**
     * @param string $code
     * @param User|null $user
     *
     * @return OrderDetailObject|null
    */
    public function handle(string $code, ?User $user = null): ?OrderDetailObject;
}
