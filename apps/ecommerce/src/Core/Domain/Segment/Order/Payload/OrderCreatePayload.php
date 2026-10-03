<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Payload;

use App\Core\Domain\{
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\Address\OrderShippingObject,
    Segment\Order\ValueObject\OrderPersonalObject
};

final readonly class OrderCreatePayload
{
    public function __construct(
        public OrderPersonalObject $personal,
        public bool $sendShipping,
        public OrderPaymentMethod $paymentMethod,
        public OrderBillingObject $billing,
        public ?OrderShippingObject $shipping = null,
    ) {}

    public function shouldSendShipping(): bool
    {
        return $this->sendShipping;
    }
}
