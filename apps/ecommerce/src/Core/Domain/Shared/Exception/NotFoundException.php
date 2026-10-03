<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Exception;

final class NotFoundException extends \Exception
{
    private int $statusCode = 404;

    public function __construct(
        string $message = 'Resource not found.',
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
