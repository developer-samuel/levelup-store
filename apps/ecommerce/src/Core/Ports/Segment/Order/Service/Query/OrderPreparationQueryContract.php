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
    public function validateUserId(User $user): int;

    /** @return array<string, mixed> */
    public function getCartSummary(int $userId): array;

    /** @param array<string, mixed> $cartSummary */
    public function extractTotalPrice(array $cartSummary): float;
    public function resolvePaymentMethod(OrderCreatePayload $payload): OrderPaymentMethod;
    public function getPaymentMethod(string $paymentMethod): OrderPaymentMethod;
}
