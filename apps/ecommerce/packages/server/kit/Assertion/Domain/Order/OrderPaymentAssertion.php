<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Domain\Order;

final class OrderPaymentAssertion
{
    public static function assertPaymentIntent(mixed $paymentIntent): string
    {
        if (
            is_object($paymentIntent) && property_exists($paymentIntent, 'id') &&
            is_string($paymentIntent->id) && $paymentIntent->id !== ''
        ) {
            return $paymentIntent->id;
        }

        if (is_string($paymentIntent) && $paymentIntent !== '') {
            return $paymentIntent;
        }

        throw new \InvalidArgumentException('Invalid payment intent format.');
    }
}
