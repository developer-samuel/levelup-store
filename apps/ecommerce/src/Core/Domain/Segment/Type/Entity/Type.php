<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Type\Entity;

use Doctrine\{
    Common\Collections\ArrayCollection,
    Common\Collections\Collection,
    ORM\Mapping as ORM
};

use App\Core\Domain\{
    Segment\Category\Entity\Category,
    Segment\Category\Traits\CategoryTrait,
    Segment\Product\Entity\Product,
    Segment\Product\Traits\ProductCollectionTrait,
    Segment\Subtype\Entity\Subtype,
    Segment\Subtype\Traits\SubtypeCollectionTrait,
    Shared\Traits\Details\NameTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'types')]
#[ORM\HasLifecycleCallbacks]
class Type
{
    use IdTrait;
    use NameTrait;
    use CategoryTrait;
    use ProductCollectionTrait;
    use SubtypeCollectionTrait;
    use CreatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(
        name: 'category_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    private Category $category;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $name;

    /**
     * @var Collection<int, Product>
    */
    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Product::class)]
    private Collection $products;

    /**
     * @var Collection<int, Subtype>
    */
    #[ORM\OneToMany(
        mappedBy: 'type',
        targetEntity: Subtype::class,
        cascade: [
            'persist',
            'remove',
        ],
        orphanRemoval: true,
        fetch: 'LAZY',
    )]
    private Collection $subtypes;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->subtypes = new ArrayCollection();
    }
}
