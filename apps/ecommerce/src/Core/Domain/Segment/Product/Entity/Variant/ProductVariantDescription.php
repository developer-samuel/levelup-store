<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Entity\Variant;

use Doctrine\ORM\Mapping as ORM;

use App\Core\Domain\{
    Segment\Product\Traits\Variant\ProductVariantTrait,
    Shared\Traits\Details\BodyTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\State\PositionTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'product_variant_descriptions')]
#[ORM\HasLifecycleCallbacks]
class ProductVariantDescription
{
    use IdTrait;
    use PositionTrait;
    use BodyTrait;
    use ProductVariantTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ProductVariant::class, inversedBy: 'descriptions')]
    #[ORM\JoinColumn(
        name: 'variant_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    private ProductVariant $variant;

    #[ORM\Column(type: 'smallint')]
    private int $position;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $body;

    /**
     * @return string|null
    */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return self
    */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
}
