<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Subscriber;

use Doctrine\{
    Bundle\DoctrineBundle\Attribute\AsDoctrineListener,
    ORM\Events
};

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Message\OrderIndexMessage,
    Segment\Order\Message\OrderRemoveMessage
};

use App\Infrastructure\Abstract\Subscriber\AbstractIndexSubscriber;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::postRemove)]
final class OrderIndexSubscriber extends AbstractIndexSubscriber
{
    /**
     * @return class-string
    */
    protected function getEntityClass(): string
    {
        return Order::class;
    }

    /**
     * @param int $id
     *
     * @return OrderIndexMessage
    */
    protected function createIndexMessage(int $id): object
    {
        return new OrderIndexMessage($id);
    }

    /**
     * @param int $id
     *
     * @return OrderRemoveMessage
    */
    protected function createRemoveMessage(int $id): object
    {
        return new OrderRemoveMessage($id);
    }
}
