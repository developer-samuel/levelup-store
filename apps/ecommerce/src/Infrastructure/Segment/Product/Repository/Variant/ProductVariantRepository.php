<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Repository\Variant;

use Doctrine\{
    ORM\QueryBuilder,
    Persistence\ManagerRegistry
};

use Packages\Kit\Utils\Shared\StringNormalizer;

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

/** @extends AbstractRepository<ProductVariant> */
final class ProductVariantRepository extends AbstractRepository implements ProductVariantRepositoryContract
{
    use OrderedQuery;
    use SingleResult;

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

    protected function getAlias(): string
    {
        return 'v';
    }

    protected function getFindAllSortColumn(): string
    {
        return 'createdAt';
    }

    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::DESC;
    }

    /**
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

            $items = $ids === [] ? [] : $this->findByOrderedIds($ids);

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

    /** @return ProductVariant[] */
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

    public function getMaxPriceForFilter(ProductFilterObject $filter): float
    {
        $qb = $this->createAvailableVariantsQueryBuilder();

        $qb->select('MAX(v.price - COALESCE(d.price, 0)) AS maxPrice');

        $category = $this->normalizeScalar($filter->category);
        $type = $this->normalizeScalar($filter->type);

        if ($category !== null) {
            $qb->andWhere("REPLACE(LOWER(c.name), ' ', '-') = :category")->setParameter('category', $category);
        }

        if ($type !== null) {
            $qb->andWhere("REPLACE(LOWER(t.name), ' ', '-') = :type")->setParameter('type', $type);
        }

        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    /** @return ProductVariant[] */
    public function searchByName(string $searchTerm): array
    {
        if ($this->elasticsearch->isEnabled()) {
            ['ids' => $ids] = $this->projectionQuery->search($searchTerm, 50);

            if ($ids === []) {
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

    public function findById(int $id): ?ProductVariant
    {
        return $this->find($id);
    }

    /** @param int[] $excludedVariantIds */
    public function findRandomAvailableExcluding(array $excludedVariantIds): ?ProductVariant
    {
        $qb = $this->createQueryBuilder('v');

        ProductVariantAvailabilitySpecification::applyInStock($qb, 'v');

        if ($excludedVariantIds !== []) {
            $qb->andWhere('v.id NOT IN (:excluded)')
                ->setParameter('excluded', $excludedVariantIds);
        }

        /** @var ProductVariant[] $results */
        $results = $qb->getQuery()->getResult();

        if ($results === []) {
            return null;
        }

        return $results[array_rand($results)];
    }

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

    private function applyEffectivePrice(QueryBuilder $qb): void
    {
        $qb->addSelect('(v.price - COALESCE(d.price, 0)) AS HIDDEN effectivePrice');
    }

    private function applyAverageRating(QueryBuilder $qb): void
    {
        $qb->addSelect('(
            SELECT COALESCE(AVG(r.value), 0.0)
            FROM ' . Review::class . ' r
            WHERE r.variant = v
        ) AS HIDDEN avgRating');
    }

    private function applyFilters(QueryBuilder $qb, ProductFilterObject $filter): void
    {
        $brands = $this->normalizeArray($filter->brands);
        $subtypes = $this->normalizeArray($filter->subtypes);
        $category = $this->normalizeScalar($filter->category);
        $type = $this->normalizeScalar($filter->type);

        if ($filter->isDiscountRoute) {
            $qb->andWhere('d.id IS NOT NULL');
        }

        if ($brands !== []) {
            $qb->andWhere("REPLACE(LOWER(b.name), ' ', '-') IN (:brands)")->setParameter('brands', $brands);
        }

        if ($subtypes !== []) {
            $qb->andWhere("REPLACE(LOWER(st.name), ' ', '-') IN (:subtypes)")->setParameter('subtypes', $subtypes);
        }

        if ($category !== null) {
            $qb->andWhere("REPLACE(LOWER(c.name), ' ', '-') = :category")->setParameter('category', $category);
        }

        if ($type !== null) {
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

        return array_values(array_unique(array_filter($normalized, static fn(string $s): bool => $s !== '')));
    }

    private function normalizeScalar(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return StringNormalizer::toLowerCase($value);
    }

    private function applySorting(QueryBuilder $qb, ProductSortOption $sort): void
    {
        match ($sort) {
            ProductSortOption::TOP_RATED      => $qb->orderBy('avgRating', SortDirection::DESC->sort())->addOrderBy('v.createdAt', SortDirection::DESC->sort()),
            ProductSortOption::CHEAPEST       => $qb->orderBy('effectivePrice', SortDirection::ASC->sort()),
            ProductSortOption::MOST_EXPENSIVE => $qb->orderBy('effectivePrice', SortDirection::DESC->sort()),
            ProductSortOption::LATEST         => $qb->orderBy('v.createdAt', SortDirection::DESC->sort()),
        };
    }

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
