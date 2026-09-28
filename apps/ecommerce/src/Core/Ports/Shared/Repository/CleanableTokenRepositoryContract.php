<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Repository;

interface CleanableTokenRepositoryContract
{
    /**
     * @return int
    */
    public function deleteExpired(): int;
}
