<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Query;

use App\Core\Domain\{
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\User\Entity\User
};

interface OrderPreparationQueryContract
{
    /**
     * @param User $user
     *
     * @return int
    */
    public function validateUserId(User $user): int;

    /**
     * @param int $userId
     *
     * @return array<string, mixed>
    */
    public function getCartSummary(int $userId): array;

    /**
     * @param array<string, mixed> $cartSummary
     *
     * @return float
    */
    public function extractTotalPrice(array $cartSummary): float;

    /**
     * @param OrderCreatePayload $payload
     *
     * @return OrderPaymentMethod
    */
    public function resolvePaymentMethod(OrderCreatePayload $payload): OrderPaymentMethod;

    /**
     * @param string $paymentMethod
     *
     * @return OrderPaymentMethod
    */
    public function getPaymentMethod(string $paymentMethod): OrderPaymentMethod;
}
