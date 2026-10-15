<?php

declare(strict_types=1);

namespace App\Core\Application\Search\Handler\Query;

use App\Core\Ports\{
    Search\Handler\Query\SearchRenderQueryHandlerContract,
    Search\Service\SearchQueryContract,
    Web\Search\Renderer\SearchRendererContract
};

final readonly class SearchRenderQueryHandler implements SearchRenderQueryHandlerContract
{
    public function __construct(
        private SearchQueryContract $searchQuery,
        private SearchRendererContract $searchRenderer,
    ) {}

    /** @return array{html: string, hasMore: bool} */
    public function handle(string $query, int $page, int $limit): array
    {
        $query = trim($query);

        if ($query === '') {
            return $this->buildResponse([], false);
        }

        ['items' => $items, 'hasMore' => $hasMore] = $this->searchQuery->searchByTerm($query, $page, $limit);

        return $this->buildResponse($items, $hasMore);
    }

    /**
     * @param array<int, mixed> $items
     *
     * @return array{html: string, hasMore: bool}
    */
    private function buildResponse(array $items, bool $hasMore): array
    {
        $html = $this->searchRenderer->renderSearchPanelView($items);

        return ['html' => $html, 'hasMore' => $hasMore];
    }
}
