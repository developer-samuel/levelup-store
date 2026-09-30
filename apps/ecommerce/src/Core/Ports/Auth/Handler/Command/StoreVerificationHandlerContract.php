<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Handler\Command;

interface StoreVerificationHandlerContract
{
    /**
     * @return array<string, mixed>
    */
    public function handle(): array;
}
