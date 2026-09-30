<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared;

interface ReindexableInterface
{
    /**
     * @return int
    */
    public function reindexAll(): int;

    /**
     * @return string
    */
    public function getIndexName(): string;
}
