<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Query;

use Kit\{
    Assertion\Shared\IdAssertion,
    Utils\Shared\StringNormalizer,
    Utils\Shared\DataSanitizer
};

use App\Core\Domain\{
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Segment\Cart\Service\Query\CartSummaryQueryContract,
    Segment\Order\Service\Query\OrderPreparationQueryContract
};

final readonly class OrderPreparationQueryService implements OrderPreparationQueryContract
{
    /**
     * @param CartSummaryQueryContract $cartSummaryQuery
    */
    public function __construct(
        private CartSummaryQueryContract $cartSummaryQuery,
    ) {}

    /**
     * @param User $user
     *
     * @return int
    */
    public function validateUserId(User $user): int
    {
        return IdAssertion::assert($user->getId(), 'User ID');
    }

    /**
     * @param int $userId
     *
     * @return array<string, mixed>
    */
    public function getCartSummary(int $userId): array
    {
        return $this->cartSummaryQuery->getCartSummary($userId);
    }

    /**
     * @param array<string, mixed> $cartSummary
     *
     * @return float
    */
    public function extractTotalPrice(array $cartSummary): float
    {
        if (!isset($cartSummary['totalPrice'])) {
            return 0.0;
        }

        return DataSanitizer::sanitizeFloat($cartSummary['totalPrice']) ?? 0.0;
    }

    /**
     * @param OrderCreatePayload $payload
     *
     * @return OrderPaymentMethod
    */
    public function resolvePaymentMethod(OrderCreatePayload $payload): OrderPaymentMethod
    {
        $method = StringNormalizer::toLowerCase(trim($payload->paymentMethod->value));

        $resolved = OrderPaymentMethod::tryFrom($method);
        if (!$resolved) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid payment method "%s". Allowed values: %s',
                $method,
                implode(', ', array_map(fn($e) => $e->value, OrderPaymentMethod::cases())),
            ));
        }

        return $resolved;
    }

    /**
     * @param string $paymentMethod
     *
     * @return OrderPaymentMethod
    */
    public function getPaymentMethod(string $paymentMethod): OrderPaymentMethod
    {
        return OrderPaymentMethod::from(StringNormalizer::toLowerCase($paymentMethod));
    }
}
