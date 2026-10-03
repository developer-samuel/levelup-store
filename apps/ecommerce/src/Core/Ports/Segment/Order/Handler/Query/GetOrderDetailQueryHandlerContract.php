<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Handler\Query;

use App\Core\Domain\{
    Segment\Order\ValueObject\OrderDetailObject,
    Segment\User\Entity\User
};

interface GetOrderDetailQueryHandlerContract
{
    public function handle(string $code, ?User $user = null): ?OrderDetailObject;
}
