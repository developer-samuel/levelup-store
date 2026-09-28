<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Cart\Entity;

use Doctrine\{
    Common\Collections\ArrayCollection,
    Common\Collections\Collection,
    ORM\Mapping as ORM
};

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Traits\UserTrait,
    Shared\Traits\Collection\ItemCollectionTrait,
    Shared\Traits\Identity\IdTrait,
    Shared\Traits\Timestamps\CreatedTimestampTrait,
    Shared\Traits\Timestamps\UpdatedTimestampTrait
};

/**
 * @SuppressWarnings("UnusedPrivateField")
 *
 * @property Collection<int, CartItem> $items
*/
#[ORM\Entity]
#[ORM\Table(name: 'carts')]
#[ORM\HasLifecycleCallbacks]
class Cart
{
    use IdTrait;
    use UserTrait;
    use CreatedTimestampTrait;
    use UpdatedTimestampTrait;

    /**
     * @use ItemCollectionTrait<CartItem>
    */
    use ItemCollectionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $reminderSentAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private User $user;

    /**
     * @var Collection<int, CartItem>
    */
    #[ORM\OneToMany(
        mappedBy: 'cart',
        targetEntity: CartItem::class,
        cascade: [
            'persist',
            'remove',
        ],
        orphanRemoval: true,
        fetch: 'LAZY',
    )]
    #[ORM\OrderBy(['id' => 'DESC'])]
    private Collection $items;

    public function __construct(User $user)
    {
        $this->user = $user;

        $this->items = new ArrayCollection();
    }

    /**
     * @return \DateTimeImmutable|null
    */
    public function getReminderSentAt(): ?\DateTimeImmutable
    {
        return $this->reminderSentAt;
    }

    /**
     * @return self
    */
    public function markReminderSent(): self
    {
        $this->reminderSentAt = new \DateTimeImmutable();

        return $this;
    }
}
