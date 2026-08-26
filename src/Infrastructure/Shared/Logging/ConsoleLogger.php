<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Logging;

use App\Core\Ports\Shared\Logging\ConsoleLoggerContract;

final class ConsoleLogger implements ConsoleLoggerContract
{
    /**
     * @param string $message
     *
     * @return void
    */
    public function logMessage(string $message): void
    {
        echo $message . PHP_EOL;
    }

    /**
     * @param string $message
     *
     * @return void
    */
    public function logSuccess(string $message): void
    {
        echo "✅\u{00A0} " . $message . PHP_EOL;
    }

    /**
     * @param string $message
     *
     * @return void
    */
    public function logWarning(string $message): void
    {
        echo "⚠️\u{00A0} " . $message . PHP_EOL;
    }

    /**
     * @param string $message
     *
     * @return void
    */
    public function logError(string $message): void
    {
        echo "❌\u{00A0} " . $message . PHP_EOL;
    }
}
