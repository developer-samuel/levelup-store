<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Repository\Variant;

use Doctrine\{
    ORM\QueryBuilder,
    Persistence\ManagerRegistry
};

use Kit\Utils\Shared\Normalizer\StringNormalizer;

use App\Core\Domain\{
    Segment\Product\Entity\Product,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Enum\ProductSortOption,
    Segment\Product\Specification\ProductVariantAvailabilitySpecification,
    Segment\Product\ValueObject\ProductFilterObject,
    Segment\Review\Entity\Review
};

use App\Core\Ports\{
    Gateways\External\Search\ElasticsearchGatewayContract,
    Segment\Product\Projection\ProductVariantProjectionQueryContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract
};

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection,
    Shared\Traits\OrderedQuery,
    Shared\Traits\SingleResult
};

/**
 * @extends AbstractRepository<ProductVariant>
*/
class ProductVariantRepository extends AbstractRepository implements ProductVariantRepositoryContract
{
    use OrderedQuery;
    use SingleResult;

    /**
     * @param ElasticsearchGatewayContract $elasticsearch
     * @param ProductVariantProjectionQueryContract $projectionQuery
     * @param ManagerRegistry $registry
    */
    public function __construct(
        private readonly ElasticsearchGatewayContract $elasticsearch,
        private readonly ProductVariantProjectionQueryContract $projectionQuery,
        ManagerRegistry $registry,
    ) {
        parent::__construct(
            $registry,
            ProductVariant::class,
        );
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 'v';
    }

    /**
     * @return string
    */
    protected function getFindAllSortColumn(): string
    {
        return 'createdAt';
    }

    /**
     * @return SortDirection
    */
    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::DESC;
    }

    /**
     * @param ProductFilterObject $filter
     * @param int $page
     * @param int $limit
     * @param ProductSortOption|null $sort
     *
     * @return array{
     *     items: ProductVariant[],
     *     total: int
     * }
    */
    public function findAvailableVariantsPaginated(
        ProductFilterObject $filter,
        int $page,
        int $limit,
        ?ProductSortOption $sort = null,
    ): array {
        if ($this->elasticsearch->isEnabled()) {
            ['ids' => $ids, 'total' => $total] = $this->projectionQuery->filter(
                $filter,
                $page,
                $limit,
                $sort,
            );

            $items = empty($ids) ? [] : $this->findByOrderedIds($ids);

            return ['items' => $items, 'total' => $total];
        }

        $qb = $this->createAvailableVariantsQueryBuilder();

        $this->applyEffectivePrice($qb);
        $this->applyAverageRating($qb);

        ProductVariantAvailabilitySpecification::applyInStock($qb, 'v');

        $this->applyFilters($qb, $filter);

        $total = $this->getTotalCount($qb);

        $sort ??= ProductSortOption::TOP_RATED;
        $this->applySorting($qb, $sort);

        $this->applyPagination($qb, $page, $limit);

        /** @var ProductVariant[] $items */
        $items = $qb->getQuery()->getResult();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * @param Product $product
     *
     * @return ProductVariant[]
    */
    public function findAllByProduct(?Product $product = null): array
    {
        $qb = $this->createQueryBuilder('v')
            ->andWhere('v.product = :product')
            ->setParameter('product', $product);

        ProductVariantAvailabilitySpecification::applyInStock($qb, 'v');

        /** @var ProductVariant[] $results */
        $results = $this->getOrderedResults($qb, 'v', ProductVariant::class);

        return $results;
    }

    /**
     * @param ProductFilterObject $filter
     *
     * @return float
    */
    public function getMaxPriceForFilter(ProductFilterObject $filter): float
    {
        $qb = $this->createAvailableVariantsQueryBuilder();

        $qb->select('MAX(v.price - COALESCE(d.price, 0)) AS maxPrice');

        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param string $searchTerm
     *
     * @return ProductVariant[]
    */
    public function searchByName(string $searchTerm): array
    {
        if ($this->elasticsearch->isEnabled()) {
            ['ids' => $ids] = $this->projectionQuery->search($searchTerm, 50);

            if (empty($ids)) {
                return [];
            }

            return $this->findByOrderedIds($ids);
        }

        $qb = $this->createQueryBuilder('v')
            ->andWhere('LOWER(v.name) LIKE LOWER(:searchTerm)')
            ->setParameter('searchTerm', '%' . $searchTerm . '%');

        ProductVariantAvailabilitySpecification::applyInStock($qb, 'v');

        /** @var ProductVariant[] $results */
        $results = $this->getOrderedResults($qb, 'v', ProductVariant::class);

        return $results;
    }

    /**
     * @param string $url
     *
     * @return ProductVariant|null
    */
    public function findOneByUrl(string $url): ?ProductVariant
    {
        $qb = $this->createQueryBuilder('v')
            ->andWhere('LOWER(v.url) = LOWER(:url)')
            ->setParameter('url', $url);

        $variant = $this->getResultOrNull($qb);
        if (!$variant instanceof ProductVariant) {
            return null;
        }

        return ProductVariantAvailabilitySpecification::findOneInStock($variant);
    }

    /**
     * @param int $id
     *
     * @return ProductVariant|null
    */
    public function findById(int $id): ?ProductVariant
    {
        return $this->find($id);
    }

    /**
     * @param int[] $excludedVariantIds
     *
     * @return ProductVariant|null
    */
    public function findRandomAvailableExcluding(array $excludedVariantIds): ?ProductVariant
    {
        $qb = $this->createQueryBuilder('v');

        ProductVariantAvailabilitySpecification::applyInStock($qb, 'v');

        if (!empty($excludedVariantIds)) {
            $qb->andWhere('v.id NOT IN (:excluded)')
                ->setParameter('excluded', $excludedVariantIds);
        }

        /** @var ProductVariant[] $results */
        $results = $qb->getQuery()->getResult();

        if (empty($results)) {
            return null;
        }

        return $results[array_rand($results)];
    }

    /**
     * @return QueryBuilder
    */
    private function createAvailableVariantsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('v')
            ->innerJoin('v.product', 'p')
            ->leftJoin('v.discount', 'd')
            ->leftJoin('p.brand', 'b')
            ->leftJoin('p.type', 't')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.subtypes', 'ps')
            ->leftJoin('ps.subtype', 'st')
            ->leftJoin('v.images', 'vi')
            ->addSelect('v', 'p', 'd', 'b', 't', 'c', 'ps', 'st', 'vi');
    }

    /**
     * @param QueryBuilder $qb
     *
     * @return void
    */
    private function applyEffectivePrice(QueryBuilder $qb): void
    {
        $qb->addSelect('(v.price - COALESCE(d.price, 0)) AS HIDDEN effectivePrice');
    }

    /**
     * @param QueryBuilder $qb
     *
     * @return void
    */
    private function applyAverageRating(QueryBuilder $qb): void
    {
        $qb->addSelect('(
            SELECT COALESCE(AVG(r.value), 0.0)
            FROM ' . Review::class . ' r
            WHERE r.variant = v
        ) AS HIDDEN avgRating');
    }

    /**
     * @param QueryBuilder $qb
     * @param ProductFilterObject $filter
     *
     * @return void
    */
    private function applyFilters(QueryBuilder $qb, ProductFilterObject $filter): void
    {
        $brands = $this->normalizeArray($filter->brands);
        $subtypes = $this->normalizeArray($filter->subtypes);
        $category = $this->normalizeScalar($filter->category);
        $type = $this->normalizeScalar($filter->type);

        if ($filter->isDiscountRoute) {
            $qb->andWhere('d.id IS NOT NULL');
        }

        if ($brands) {
            $qb->andWhere("REPLACE(LOWER(b.name), ' ', '-') IN (:brands)")->setParameter('brands', $brands);
        }

        if ($subtypes) {
            $qb->andWhere("REPLACE(LOWER(st.name), ' ', '-') IN (:subtypes)")->setParameter('subtypes', $subtypes);
        }

        if ($category) {
            $qb->andWhere("REPLACE(LOWER(c.name), ' ', '-') = :category")->setParameter('category', $category);
        }

        if ($type) {
            $qb->andWhere("REPLACE(LOWER(t.name), ' ', '-') = :type")->setParameter('type', $type);
        }

        if ($filter->minPrice !== null) {
            $qb->andWhere('(v.price - COALESCE(d.price, 0)) >= :minPrice')
                ->setParameter('minPrice', $filter->minPrice);
        }

        if ($filter->maxPrice !== null) {
            $qb->andWhere('(v.price - COALESCE(d.price, 0)) <= :maxPrice')
                ->setParameter('maxPrice', $filter->maxPrice);
        }
    }

    /**
     * @param QueryBuilder $qb
     *
     * @return int
    */
    private function getTotalCount(QueryBuilder $qb): int
    {
        $countQb = clone $qb;
        $countQb->select('COUNT(DISTINCT v.id)')->resetDQLPart('orderBy');

        return (int) $countQb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param string[] $items
     *
     * @return string[]
    */
    private function normalizeArray(array $items): array
    {
        $normalized = array_map(
            static fn($item): string => StringNormalizer::toLowerCase($item),
            $items,
        );

        return array_values(array_unique(array_filter($normalized)));
    }

    /**
     * @param string|null $value
     *
     * @return string|null
    */
    private function normalizeScalar(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return StringNormalizer::toLowerCase($value);
    }

    /**
     * @param QueryBuilder $qb
     * @param ProductSortOption $sort
     *
     * @return void
    */
    private function applySorting(QueryBuilder $qb, ProductSortOption $sort): void
    {
        match ($sort) {
            ProductSortOption::TOP_RATED      => $qb->orderBy('avgRating', 'DESC')->addOrderBy('v.createdAt', 'DESC'),
            ProductSortOption::CHEAPEST       => $qb->orderBy('effectivePrice', 'ASC'),
            ProductSortOption::MOST_EXPENSIVE => $qb->orderBy('effectivePrice', 'DESC'),
            ProductSortOption::LATEST         => $qb->orderBy('v.createdAt', 'DESC'),
        };
    }

    /**
     * @param QueryBuilder $qb
     * @param int $page
     * @param int $limit
     *
     * @return void
    */
    private function applyPagination(QueryBuilder $qb, int $page, int $limit): void
    {
        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
    }

    /**
     * @param int[] $ids
     *
     * @return ProductVariant[]
    */
    private function findByOrderedIds(array $ids): array
    {
        /** @var ProductVariant[] $variants */
        $variants = $this->createQueryBuilder('v')
            ->andWhere('v.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        $indexed = [];
        foreach ($variants as $variant) {
            $indexed[$variant->getId()] = $variant;
        }

        $ordered = [];
        foreach ($ids as $id) {
            if (isset($indexed[$id])) {
                $ordered[] = $indexed[$id];
            }
        }

        return $ordered;
    }
}
