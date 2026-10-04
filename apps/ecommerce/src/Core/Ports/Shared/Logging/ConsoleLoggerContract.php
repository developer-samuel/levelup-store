<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Logging;

interface ConsoleLoggerContract
{
    public function logMessage(string $message): void;
    public function logSuccess(string $message): void;
    public function logWarning(string $message): void;
    public function logError(string $message): void;
}
