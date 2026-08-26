<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Projection;

use Elastic\{
    Elasticsearch\Client,
    Elasticsearch\Response\Elasticsearch
};

use App\Core\Domain\{
    Segment\Product\Enum\ProductSortOption,
    Segment\Product\ValueObject\ProductFilterObject
};

use App\Core\Ports\Segment\Product\Projection\ProductVariantProjectionQueryContract;

final readonly class ProductVariantProjectionQuery implements ProductVariantProjectionQueryContract
{
    /**
     * @param Client $client
    */
    public function __construct(
        private Client $client,
    ) {}

    /**
     * @param string $term
     * @param int $limit
     *
     * @return array{ids: int[], total: int}
    */
    public function search(string $term, int $limit): array
    {
        /** @var Elasticsearch $response */
        $response = $this->client->search([
            'index' => ProductVariantProjection::NAME,
            'body'  => [
                'size'  => $limit,
                'query' => [
                    'bool' => [
                        'should'   => [
                            ['match_phrase_prefix' => [
                                'name' => ['query' => $term, 'boost' => 3],
                            ]],
                            ['multi_match' => [
                                'query'     => $term,
                                'fields'    => ['name^3'],
                                'type'      => 'best_fields',
                                'fuzziness' => 'AUTO',
                            ]],
                        ],
                        'minimum_should_match' => 1,
                        'filter' => [
                            ['term' => ['is_available' => true]],
                        ],
                    ],
                ],
            ],
        ]);

        return $this->extractResult($response->asArray());
    }

    /**
     * @param ProductFilterObject $filter
     * @param int $page
     * @param int $limit
     * @param ProductSortOption|null $sort
     *
     * @return array{ids: int[], total: int}
    */
    public function filter(
        ProductFilterObject $filter,
        int $page,
        int $limit,
        ?ProductSortOption $sort,
    ): array {
        /** @var Elasticsearch $response */
        $response = $this->client->search([
            'index' => ProductVariantProjection::NAME,
            'body'  => [
                'from'  => ($page - 1) * $limit,
                'size'  => $limit,
                'query' => ['bool' => ['filter' => $this->buildFilterClauses($filter)]],
                'sort'  => $this->buildSortClause($sort ?? ProductSortOption::TOP_RATED),
            ],
        ]);

        return $this->extractResult($response->asArray());
    }

    /**
     * @param ProductFilterObject $filter
     *
     * @return array<int, array<string, mixed>>
    */
    private function buildFilterClauses(ProductFilterObject $filter): array
    {
        $clauses = [['term' => ['is_available' => true]]];

        if ($filter->isDiscountRoute) {
            $clauses[] = ['term' => ['has_discount' => true]];
        }

        if (!empty($filter->brands)) {
            $clauses[] = ['terms' => ['brand' => array_values($filter->brands)]];
        }

        if (!empty($filter->subtypes)) {
            $clauses[] = ['terms' => ['subtypes' => array_values($filter->subtypes)]];
        }

        if ($filter->category !== null) {
            $clauses[] = ['term' => ['category' => $filter->category]];
        }

        if ($filter->type !== null) {
            $clauses[] = ['term' => ['type' => $filter->type]];
        }

        $priceRange = $this->buildPriceRange($filter);
        if ($priceRange !== null) {
            $clauses[] = ['range' => ['effective_price' => $priceRange]];
        }

        return $clauses;
    }

    /**
     * @param ProductFilterObject $filter
     *
     * @return array<string, float>|null
    */
    private function buildPriceRange(ProductFilterObject $filter): ?array
    {
        $range = [];

        if ($filter->minPrice !== null) {
            $range['gte'] = $filter->minPrice;
        }

        if ($filter->maxPrice !== null) {
            $range['lte'] = $filter->maxPrice;
        }

        return empty($range) ? null : $range;
    }

    /**
     * @param ProductSortOption $sort
     *
     * @return array<int, array<string, mixed>>
    */
    private function buildSortClause(ProductSortOption $sort): array
    {
        return match ($sort) {
            ProductSortOption::TOP_RATED      => [['avg_rating' => 'desc'], ['created_at' => 'desc']],
            ProductSortOption::CHEAPEST       => [['effective_price' => 'asc']],
            ProductSortOption::MOST_EXPENSIVE => [['effective_price' => 'desc']],
            ProductSortOption::LATEST         => [['created_at' => 'desc']],
        };
    }

    /**
     * @param array<array-key, mixed> $raw
     *
     * @return array{ids: int[], total: int}
    */
    private function extractResult(array $raw): array
    {
        /** @var array{hits: array<int, array<string, mixed>>, total: array{value: int}} $hitsWrapper */
        $hitsWrapper = $raw['hits'];
        $hits        = $hitsWrapper['hits'];

        /** @var array{value: int} $totalData */
        $totalData = $hitsWrapper['total'];
        $total     = $totalData['value'];

        $ids = array_map(
            static function (array $hit): int {
                /** @var array{_id: string|int} $hit */
                return (int) $hit['_id'];
            },
            $hits,
        );

        return ['ids' => $ids, 'total' => $total];
    }
}
