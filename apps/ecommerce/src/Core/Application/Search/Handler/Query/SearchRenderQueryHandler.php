<?php

declare(strict_types=1);

namespace App\Core\Application\Search\Handler\Query;

use App\Core\Ports\{
    Search\Handler\Query\SearchRenderQueryHandlerContract,
    Search\Renderer\SearchRendererContract,
    Search\Service\SearchQueryContract
};

final readonly class SearchRenderQueryHandler implements SearchRenderQueryHandlerContract
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
     * @return string[]
    */
    public function handle(string $query): array
    {
        $query = trim($query);

        if ($query === '') {
            return $this->buildResponse([]);
        }

        $searchItems = $this->searchQuery->searchByTerm($query);
        return $this->buildResponse($searchItems);
    }

    /**
     * @param array<int, mixed> $items
     *
     * @return string[]
    */
    private function buildResponse(array $items): array
    {
        $html = $this->searchRenderer->renderSearchPanelView($items);

        return ['html' => $html];
    }
}
