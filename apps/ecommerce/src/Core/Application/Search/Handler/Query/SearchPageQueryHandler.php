<?php

declare(strict_types=1);

namespace App\Core\Application\Search\Handler\Query;

use App\Core\Ports\{
    Search\Handler\Query\SearchPageQueryHandlerContract,
    Search\Service\SearchQueryContract,
    Web\Search\Renderer\SearchRendererContract
};

final readonly class SearchPageQueryHandler implements SearchPageQueryHandlerContract
{
    public function __construct(
        private SearchQueryContract $searchQuery,
        private SearchRendererContract $searchRenderer,
    ) {}

    public function handle(string $query): string
    {
        $results = $this->getSearchResults(
            trim($query),
        );

        return $this->renderResults($results);
    }

    /** @return array<int, mixed> */
    private function getSearchResults(string $query): array
    {
        return $query !== '' ? $this->searchQuery->searchByTerm($query) : [];
    }

    /** @param array<int, mixed> $results */
    private function renderResults(array $results): string
    {
        return $this->searchRenderer->renderIndexView($results);
    }
}
