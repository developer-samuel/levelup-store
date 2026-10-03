<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Repository;

interface CleanableTokenRepositoryContract
{
    public function deleteExpired(): int;
}
