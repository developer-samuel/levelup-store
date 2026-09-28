<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Query;

use App\Core\Domain\{
    Segment\Country\Entity\Country,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\Address\OrderShippingObject
};

interface OrderCountryQueryContract
{
    /**
     * @param OrderBillingObject|OrderShippingObject $data
     *
     * @return Country
    */
    public function getCountryFromData(OrderBillingObject|OrderShippingObject $data): Country;
}
