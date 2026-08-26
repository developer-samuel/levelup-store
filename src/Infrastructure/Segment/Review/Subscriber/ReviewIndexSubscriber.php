<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\Subscriber;

use Doctrine\{
    Bundle\DoctrineBundle\Attribute\AsDoctrineListener,
    ORM\Events
};

use App\Core\Domain\{
    Segment\Review\Entity\Review,
    Segment\Review\Message\ReviewIndexMessage,
    Segment\Review\Message\ReviewRemoveMessage
};

use App\Infrastructure\Abstract\Subscriber\AbstractIndexSubscriber;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::postRemove)]
final class ReviewIndexSubscriber extends AbstractIndexSubscriber
{
    /**
     * @return class-string
    */
    protected function getEntityClass(): string
    {
        return Review::class;
    }

    /**
     * @param int $id
     *
     * @return ReviewIndexMessage
    */
    protected function createIndexMessage(int $id): object
    {
        return new ReviewIndexMessage($id);
    }

    /**
     * @param int $id
     *
     * @return ReviewRemoveMessage
    */
    protected function createRemoveMessage(int $id): object
    {
        return new ReviewRemoveMessage($id);
    }
}
