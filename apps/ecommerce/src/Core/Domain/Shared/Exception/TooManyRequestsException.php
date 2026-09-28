<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Exception;

use App\Shared\Utils\Formatter\DateTimeFormatter;

final class TooManyRequestsException extends \Exception
{
    private int $statusCode = 429;

    /**
     * @param int $retryAfterSeconds
    */
    public function __construct(
        int $retryAfterSeconds = 0,
    ) {
        parent::__construct($this->buildMessage($retryAfterSeconds));
    }

    /**
     * @return int
    */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $retryAfterSeconds
     *
     * @return string
    */
    private function buildMessage(int $retryAfterSeconds): string
    {
        return $retryAfterSeconds > 0
            ? sprintf('Too many attempts, please try again in %s.', DateTimeFormatter::formatDuration($retryAfterSeconds))
            : 'Too many attempts, please try again in a moment.';
    }
}
