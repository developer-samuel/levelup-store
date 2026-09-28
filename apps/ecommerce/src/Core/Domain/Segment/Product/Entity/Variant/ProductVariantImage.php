<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Entity\Variant;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Product\Traits\Variant\ProductVariantTrait,
    Shared\Traits\Details\PathTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\State\PositionTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'product_variant_images')]
#[ORM\HasLifecycleCallbacks]
class ProductVariantImage
{
    use IdTrait;
    use ProductVariantTrait;
    use PathTrait;
    use PositionTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ProductVariant::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ProductVariant $variant;

    #[ORM\Column(type: 'smallint')]
    private int $position;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private string $path;
}
