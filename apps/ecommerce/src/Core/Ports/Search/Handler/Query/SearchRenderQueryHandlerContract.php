<?php

declare(strict_types=1);

namespace App\Core\Ports\Search\Handler\Query;

interface SearchRenderQueryHandlerContract
{
    /** @return array{html: string, hasMore: bool} */
    public function handle(string $query, int $page, int $limit): array;
}
