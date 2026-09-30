<?php

declare(strict_types=1);

namespace App\Core\Ports\Search\Handler\Query;

interface SearchPageQueryHandlerContract
{
    /**
     * @param string $query
     *
     * @return string
    */
    public function handle(string $query): string;
}
