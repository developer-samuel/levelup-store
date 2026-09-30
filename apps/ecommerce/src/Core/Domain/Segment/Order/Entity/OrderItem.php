<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Order\Traits\OrderTrait,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\Product\Traits\Variant\ProductVariantTrait,
    Shared\Traits\Details\PriceTrait,
    Shared\Traits\Identity\IdTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'order_items')]
class OrderItem
{
    use IdTrait;
    use OrderTrait;
    use ProductVariantTrait;
    use PriceTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(
        name: 'order_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: ProductVariant::class)]
    #[ORM\JoinColumn(name: 'variant_id', referencedColumnName: 'id', nullable: false)]
    private ProductVariant $variant;

    #[ORM\ManyToOne(targetEntity: ProductVariantEan::class)]
    #[ORM\JoinColumn(name: 'ean_id', referencedColumnName: 'id', nullable: false)]
    private ProductVariantEan $ean;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price = 0.00;

    /**
     * @return ProductVariantEan
    */
    public function getEan(): ProductVariantEan
    {
        return $this->ean;
    }

    /**
     * @param ProductVariantEan $ean
     *
     * @return self
    */
    public function setEan(ProductVariantEan $ean): self
    {
        $this->ean = $ean;
        return $this;
    }
}
