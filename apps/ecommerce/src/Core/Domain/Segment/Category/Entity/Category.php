<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Category\Entity;

use Doctrine\{
    Common\Collections\ArrayCollection,
    Common\Collections\Collection,
    ORM\Mapping as ORM
};

use App\Core\Domain\{
    Segment\Product\Entity\Product,
    Segment\Product\Traits\ProductCollectionTrait,
    Segment\Subtype\Entity\Subtype,
    Segment\Subtype\Traits\SubtypeCollectionTrait,
    Segment\Type\Entity\Type,
    Segment\Type\Traits\TypeCollectionTrait,
    Shared\Traits\Details\NameTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait
};

/** @SuppressWarnings("UnusedPrivateField") */
#[ORM\Entity]
#[ORM\Table(name: 'categories')]
#[ORM\HasLifecycleCallbacks]
class Category
{
    use IdTrait;
    use NameTrait;
    use ProductCollectionTrait;
    use TypeCollectionTrait;
    use SubtypeCollectionTrait;
    use CreatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    private string $name;

    /**
     * @var Collection<int, Product>
    */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
    private Collection $products;

    /**
     * @var Collection<int, Type>
    */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Type::class)]
    private Collection $types;

    /**
     * @var Collection<int, Subtype>
    */
    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Subtype::class)]
    private Collection $subtypes;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }
}
