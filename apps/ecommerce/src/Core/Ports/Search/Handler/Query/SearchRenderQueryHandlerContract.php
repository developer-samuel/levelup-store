<?php

declare(strict_types=1);

namespace App\Core\Ports\Search\Handler\Query;

interface SearchRenderQueryHandlerContract
{
    /**
     * @param string $query
     *
     * @return string[]
    */
    public function handle(string $query): array;
}
