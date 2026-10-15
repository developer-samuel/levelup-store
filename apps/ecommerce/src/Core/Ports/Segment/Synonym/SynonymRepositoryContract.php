<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Synonym;

use App\Core\Domain\Segment\Synonym\Synonym;

interface SynonymRepositoryContract
{
    /** @return list<Synonym> */
    public function findAll(): array;
}
