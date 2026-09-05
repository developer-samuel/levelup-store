<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Subscriber;

use Doctrine\{
    Bundle\DoctrineBundle\Attribute\AsDoctrineListener,
    ORM\Event\OnFlushEventArgs,
    ORM\Event\PostFlushEventArgs,
    ORM\Events
};

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Event\OrderStatusUpdatedEvent
};

#[AsDoctrineListener(event: Events::onFlush)]
#[AsDoctrineListener(event: Events::postFlush)]
final class OrderStatusMercureSubscriber
{
    /** @var Order[] */
    private array $pendingOrders = [];

    /**
     * @param EventDispatcherInterface $eventDispatcher
    */
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * @param OnFlushEventArgs $args
     *
     * @return void
    */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $uow = $args->getObjectManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof Order) {
                continue;
            }

            $changeSet = $uow->getEntityChangeSet($entity);

            if (isset($changeSet['status'])) {
                $this->pendingOrders[$entity->getId()] = $entity;
            }
        }
    }

    /**
     * @param PostFlushEventArgs $args
     *
     * @return void
    */
    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->pendingOrders)) {
            return;
        }

        foreach ($this->pendingOrders as $order) {
            $this->eventDispatcher->dispatch(new OrderStatusUpdatedEvent($order));
        }

        $this->pendingOrders = [];
    }
}
