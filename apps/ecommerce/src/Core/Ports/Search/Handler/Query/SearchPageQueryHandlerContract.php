<?php

declare(strict_types=1);

namespace App\Core\Ports\Search\Handler\Query;

interface SearchPageQueryHandlerContract
{
    public function handle(string $query): string;
}
