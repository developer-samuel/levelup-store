<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Handler\Command;

interface DestroyProfileHandlerContract
{
    /**
     * @return array<string, mixed>
    */
    public function handle(): array;
}
