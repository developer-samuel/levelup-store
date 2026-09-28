<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Specification;

use Doctrine\ORM\QueryBuilder;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\Product\Enum\ProductStockStatus,
    Segment\Product\Enum\Variant\ProductVariantEanStatus,
    Segment\Product\Enum\Variant\ProductVariantStatus,
};

final class ProductVariantAvailabilitySpecification
{
    /**
     * @param QueryBuilder $qb
     * @param string $alias
     *
     * @return QueryBuilder
    */
    public static function applyInStock(QueryBuilder $qb, string $alias): QueryBuilder
    {
        return self::applyInStockAndActiveEan($qb, $alias)
            ->andWhere(sprintf('%s.status = :status', $alias))
            ->setParameter('status', ProductVariantStatus::AVAILABLE)
            ->setParameter('inStockStatus', ProductStockStatus::IN_STOCK)
            ->setParameter('activeEan', ProductVariantEanStatus::ACTIVE);
    }

    /**
     * @param ProductVariant $variant
     *
     * @return ProductVariant|null
    */
    public static function findOneInStock(ProductVariant $variant): ?ProductVariant
    {
        $filtered = self::filterInStock([$variant]);

        return $filtered[0] ?? null;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $alias
     *
     * @return QueryBuilder
    */
    private static function applyInStockAndActiveEan(QueryBuilder $qb, string $alias): QueryBuilder
    {
        $qb->innerJoin($alias . '.stock', 's')
            ->andWhere('s.quantityAvailable > 0')
            ->andWhere('s.status = :inStockStatus');

        $qb->andWhere(sprintf(
            'EXISTS (SELECT 1 FROM %s e WHERE e.variant = %s AND e.status = :activeEan)',
            ProductVariantEan::class,
            $alias,
        ));

        return $qb;
    }

    /**
     * @param ProductVariant[] $variants
     *
     * @return ProductVariant[]
    */
    private static function filterInStock(array $variants): array
    {
        return array_filter(
            $variants,
            static fn(ProductVariant $v): bool => self::isInStock($v),
        );
    }

    /**
     * @param ProductVariant $variant
     *
     * @return bool
    */
    private static function isInStock(ProductVariant $variant): bool
    {
        $stock = $variant->getStock();

        return $variant->getStatus() === ProductVariantStatus::AVAILABLE
            && $stock !== null
            && $stock->getQuantityAvailable() > 0
            && $stock->getStatus() === ProductStockStatus::IN_STOCK
            && self::hasActiveEan($variant);
    }

    /**
     * @param ProductVariant $variant
     *
     * @return bool
    */
    private static function hasActiveEan(ProductVariant $variant): bool
    {
        return $variant->getEans()
            ->filter(
                fn($e) => $e->getStatus() === ProductVariantEanStatus::ACTIVE,
            )->count() > 0;
    }
}
