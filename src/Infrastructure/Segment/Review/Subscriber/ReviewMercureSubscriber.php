<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\Subscriber;

use Doctrine\{
    Bundle\DoctrineBundle\Attribute\AsDoctrineListener,
    ORM\EntityManagerInterface,
    ORM\Event\PostFlushEventArgs,
    ORM\Events,
    Persistence\Event\LifecycleEventArgs
};

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use App\Core\Domain\{
    Segment\Review\Entity\ReviewRating,
    Segment\Review\Event\ReviewRatingToggledEvent
};

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::postRemove)]
#[AsDoctrineListener(event: Events::postFlush)]
final class ReviewMercureSubscriber
{
    /** @var array<int, array{variantId: int, reviewId: int}> */
    private array $pendingRatings = [];

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
        $this->collect($args->getObject());
    }

    /**
     * @param PostFlushEventArgs $args
     *
     * @return void
    */
    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->pendingRatings)) {
            return;
        }

        foreach ($this->pendingRatings as ['variantId' => $variantId, 'reviewId' => $reviewId]) {
            $this->eventDispatcher->dispatch(new ReviewRatingToggledEvent($variantId, $reviewId));
        }

        $this->pendingRatings = [];
    }

    /**
     * @param object $entity
     *
     * @return void
    */
    private function collect(object $entity): void
    {
        if (!$entity instanceof ReviewRating) {
            return;
        }

        $review    = $entity->getReview();
        $variantId = $review->getVariant()->getId();
        $reviewId  = $review->getId();

        $this->pendingRatings[$reviewId] = [
            'variantId' => $variantId,
            'reviewId'  => $reviewId,
        ];
    }
}
