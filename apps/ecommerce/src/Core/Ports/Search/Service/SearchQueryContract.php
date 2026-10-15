<?php

declare(strict_types=1);

namespace App\Core\Ports\Search\Service;

interface SearchQueryContract
{
    /**
     * @return array{
     *     items: array<int, array<string, mixed>>,
     *     hasMore: bool
     * }
     */
    public function searchByTerm(string $query, int $page, int $limit): array;
}
