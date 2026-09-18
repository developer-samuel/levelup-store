<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Entity\Variant;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Product\Traits\Variant\ProductVariantTrait,
    Shared\Traits\Details\PriceTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'product_variant_discounts')]
#[ORM\HasLifecycleCallbacks]
class ProductVariantDiscount
{
    use IdTrait;
    use ProductVariantTrait;
    use PriceTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\OneToOne(inversedBy: 'discount', targetEntity: ProductVariant::class)]
    #[ORM\JoinColumn(
        name: 'variant_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private ProductVariant $variant;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price = 0.00;
}
