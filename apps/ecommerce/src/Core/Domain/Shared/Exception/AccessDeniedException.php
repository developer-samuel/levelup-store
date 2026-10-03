<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Exception;

final class AccessDeniedException extends \Exception
{
    private int $statusCode = 403;

    public function __construct(
        string $message = 'Access denied.',
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
