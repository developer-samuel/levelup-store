<?php

declare(strict_types=1);

namespace App\Core\Ports\Payment\Stripe;

interface StripeSdkContract
{
    public function initialize(): void;
}
