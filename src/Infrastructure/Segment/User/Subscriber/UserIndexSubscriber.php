<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\User\Subscriber;

use Doctrine\{
    Bundle\DoctrineBundle\Attribute\AsDoctrineListener,
    ORM\Events
};

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Message\UserIndexMessage,
    Segment\User\Message\UserRemoveMessage
};

use App\Infrastructure\Abstract\Subscriber\AbstractIndexSubscriber;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::postRemove)]
final class UserIndexSubscriber extends AbstractIndexSubscriber
{
    /**
     * @return class-string
    */
    protected function getEntityClass(): string
    {
        return User::class;
    }

    /**
     * @param int $id
     *
     * @return UserIndexMessage
    */
    protected function createIndexMessage(int $id): object
    {
        return new UserIndexMessage($id);
    }

    /**
     * @param int $id
     *
     * @return UserRemoveMessage
    */
    protected function createRemoveMessage(int $id): object
    {
        return new UserRemoveMessage($id);
    }
}
