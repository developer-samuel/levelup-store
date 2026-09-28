<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Entity\Variant;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Product\Enum\ProductStockStatus,
    Segment\Product\Traits\Variant\ProductVariantTrait,
    Segment\Product\Traits\Variant\ProductVariantStockTrait,
    Segment\Product\Traits\Variant\ProductVariantQuantityTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'product_variant_stocks')]
#[ORM\HasLifecycleCallbacks]
class ProductVariantStock
{
    use IdTrait;
    use ProductVariantTrait;
    use ProductVariantQuantityTrait;
    use ProductVariantStockTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\OneToOne(
        inversedBy: 'stock',
        targetEntity: ProductVariant::class,
    )]
    #[ORM\JoinColumn(
        name: 'variant_id',
        referencedColumnName: 'id',
        onDelete: 'CASCADE',
        nullable: false,
    )]
    private ProductVariant $variant;

    #[ORM\Column(
        type: 'string',
        length: 20,
        enumType: ProductStockStatus::class,
        options: ['default' => ProductStockStatus::IN_STOCK->value],
    )]
    private ProductStockStatus $status = ProductStockStatus::IN_STOCK;

    /**
     * @return ProductStockStatus
    */
    public function getStatus(): ProductStockStatus
    {
        return $this->status;
    }

    /**
     * @param ProductStockStatus $status
     *
     * @return self
    */
    public function setStatus(ProductStockStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return bool
    */
    public function isAvailable(): bool
    {
        if ($this->status === ProductStockStatus::OUT_OF_STOCK) {
            return false;
        }

        return $this->quantityAvailable > 0;
    }
}
