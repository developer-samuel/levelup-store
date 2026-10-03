<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Exception;

final class ConflictException extends \RuntimeException
{
    private int $statusCode = 409;

    public function __construct(
        string $message = 'Conflict occurred.',
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
