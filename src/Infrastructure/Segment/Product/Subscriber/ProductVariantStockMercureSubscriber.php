<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Subscriber;

use Doctrine\{
    Bundle\DoctrineBundle\Attribute\AsDoctrineListener,
    ORM\EntityManagerInterface,
    ORM\Event\PostFlushEventArgs,
    ORM\Events,
    Persistence\Event\LifecycleEventArgs
};

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\Event\ProductStockUpdatedEvent
};

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::postRemove)]
#[AsDoctrineListener(event: Events::postFlush)]
final class ProductVariantStockMercureSubscriber
{
    /** @var array<int, array{quantityAvailable: int, inStock: bool}> */
    private array $pendingStocks = [];

    /**
     * @param EventDispatcherInterface $eventDispatcher
    */
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $args
     *
     * @return void
    */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->collect($args->getObject());
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $args
     *
     * @return void
    */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->collect($args->getObject());
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $args
     *
     * @return void
    */
    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof ProductVariantStock) {
            return;
        }

        $this->pendingStocks[$entity->getVariant()->getId()] = [
            'quantityAvailable' => 0,
            'inStock'           => false,
        ];
    }

    /**
     * @param PostFlushEventArgs $args
     *
     * @return void
    */
    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->pendingStocks)) {
            return;
        }

        foreach ($this->pendingStocks as $variantId => $data) {
            $this->eventDispatcher->dispatch(new ProductStockUpdatedEvent(
                $variantId,
                $data['quantityAvailable'],
                $data['inStock'],
            ));
        }

        $this->pendingStocks = [];
    }

    /**
     * @param object $entity
     *
     * @return void
    */
    private function collect(object $entity): void
    {
        if (!$entity instanceof ProductVariantStock) {
            return;
        }

        $this->pendingStocks[$entity->getVariant()->getId()] = [
            'quantityAvailable' => $entity->getQuantityAvailable(),
            'inStock'           => $entity->isAvailable(),
        ];
    }
}
