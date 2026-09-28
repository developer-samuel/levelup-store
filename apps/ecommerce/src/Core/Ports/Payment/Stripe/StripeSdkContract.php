<?php

declare(strict_types=1);

namespace App\Core\Ports\Payment\Stripe;

interface StripeSdkContract
{
    /**
     * @return void
    */
    public function initialize(): void;
}
