<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject;

use App\Core\Domain\{
    Segment\Country\Entity\Country,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\User\Entity\User,
    Segment\User\Entity\UserBilling,
    Segment\User\Entity\UserShipping
};

final readonly class OrderCreateObject
{
    /**
     * @param User $personal
     * @param Country[] $countries
     * @param OrderPaymentMethod[] $paymentMethods
     * @param bool $cartEmpty
     * @param bool $useShipping
     * @param UserBilling|null $billing
     * @param UserShipping|null $shipping
    */
    public function __construct(
        public User $personal,
        public array $countries,
        public array $paymentMethods,
        public bool $cartEmpty,
        public bool $useShipping,
        public ?UserBilling $billing = null,
        public ?UserShipping $shipping = null,
    ) {}
}
