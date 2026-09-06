<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Logging;

interface ConsoleLoggerContract
{
    /**
     * @param string $message
     *
     * @return void
    */
    public function logMessage(string $message): void;

    /**
     * @param string $message
     *
     * @return void
    */
    public function logSuccess(string $message): void;

    /**
     * @param string $message
     *
     * @return void
    */
    public function logWarning(string $message): void;

    /**
     * @param string $message
     *
     * @return void
    */
    public function logError(string $message): void;
}
