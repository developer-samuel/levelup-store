<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Subscriber;

use Doctrine\{
    Bundle\DoctrineBundle\Attribute\AsDoctrineListener,
    ORM\Events
};

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Message\ProductVariantIndexMessage,
    Segment\Product\Message\ProductVariantRemoveMessage
};

use App\Infrastructure\Abstract\Subscriber\AbstractIndexSubscriber;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::postRemove)]
final class ProductVariantIndexSubscriber extends AbstractIndexSubscriber
{
    /**
     * @return class-string
    */
    protected function getEntityClass(): string
    {
        return ProductVariant::class;
    }

    /**
     * @param int $id
     *
     * @return ProductVariantIndexMessage
    */
    protected function createIndexMessage(int $id): object
    {
        return new ProductVariantIndexMessage($id);
    }

    /**
     * @param int $id
     *
     * @return ProductVariantRemoveMessage
    */
    protected function createRemoveMessage(int $id): object
    {
        return new ProductVariantRemoveMessage($id);
    }
}
