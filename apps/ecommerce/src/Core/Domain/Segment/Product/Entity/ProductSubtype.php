<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Product\Traits\ProductTrait,
    Segment\Subtype\Entity\Subtype,
    Segment\Subtype\Traits\SubtypeTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'product_subtypes')]
#[ORM\HasLifecycleCallbacks]
class ProductSubtype
{
    use IdTrait;
    use ProductTrait;
    use SubtypeTrait;
    use CreatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'productSubtypes')]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: Subtype::class, inversedBy: 'productSubtypes')]
    #[ORM\JoinColumn(nullable: false)]
    private Subtype $subtype;
}
