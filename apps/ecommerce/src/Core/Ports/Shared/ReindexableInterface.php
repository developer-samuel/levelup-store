<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared;

interface ReindexableInterface
{
    public function reindexAll(): int;
    public function getIndexName(): string;
}
