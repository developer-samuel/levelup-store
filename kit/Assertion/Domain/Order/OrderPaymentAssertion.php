<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\Order;

final class OrderPaymentAssertion
{
    /**
     * @param mixed $paymentIntent
     *
     * @return string
     *
     * @throws \InvalidArgumentException
    */
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
