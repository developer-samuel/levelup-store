<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Stripe;

use Stripe\Stripe;

use App\Core\Ports\Payment\Stripe\StripeSdkContract;

final class StripeSdk implements StripeSdkContract
{
    /**
     * @param string $secretKey
    */
    public function __construct(
        private string $secretKey,
    ) {}

    /**
     * @return void
     *
     * @throws \LogicException
    */
    public function initialize(): void
    {
        if (trim($this->secretKey) === '') {
            throw new \LogicException('Stripe secret key is not set.');
        }

        Stripe::setApiKey($this->secretKey);
    }
}
