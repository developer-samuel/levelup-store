<?php

declare(strict_types=1);

namespace App\Core\Application\Search\Handler\Query;

use App\Core\Ports\{
    Search\Handler\Query\SearchPageQueryHandlerContract,
    Search\Renderer\SearchRendererContract,
    Search\Service\SearchQueryContract
};

final readonly class SearchPageQueryHandler implements SearchPageQueryHandlerContract
{
    /**
     * @param SearchQueryContract $searchQuery
     * @param SearchRendererContract $searchRenderer
    */
    public function __construct(
        private SearchQueryContract $searchQuery,
        private SearchRendererContract $searchRenderer,
    ) {}

    /**
     * @param string $query
     *
     * @return string
    */
    public function handle(string $query): string
    {
        $results = $this->getSearchResults(
            trim($query),
        );

        return $this->renderResults($results);
    }

    /**
     * @param string $query
     *
     * @return array<int, mixed>
    */
    private function getSearchResults(string $query): array
    {
        return $query ? $this->searchQuery->searchByTerm($query) : [];
    }

    /**
     * @param array<int, mixed> $results
     *
     * @return string
    */
    private function renderResults(array $results): string
    {
        return $this->searchRenderer->renderIndexView($results);
    }
}
