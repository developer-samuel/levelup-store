<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Exception;

final class AccessDeniedException extends \Exception
{
    private int $statusCode = 403;

    /**
     * @param string $message
    */
    public function __construct(
        string $message = 'Access denied.',
    ) {
        parent::__construct($message);
    }

    /**
     * @return int
    */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
