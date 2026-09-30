<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Entity\Variant;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Product\Traits\Variant\ProductVariantTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\State\PositionTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'product_variant_recommended')]
#[ORM\UniqueConstraint(columns: ['variant_id', 'position'])]
#[ORM\HasLifecycleCallbacks]
class ProductVariantRecommended
{
    use IdTrait;
    use ProductVariantTrait;
    use PositionTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ProductVariant::class)]
    #[ORM\JoinColumn(
        name: 'variant_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private ProductVariant $variant;

    #[ORM\Column(type: 'smallint')]
    private int $position;
}
