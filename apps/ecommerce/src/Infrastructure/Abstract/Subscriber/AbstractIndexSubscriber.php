<?php

declare(strict_types=1);

namespace App\Infrastructure\Abstract\Subscriber;

use Doctrine\{
    ORM\EntityManagerInterface,
    Persistence\Event\LifecycleEventArgs
};

use Symfony\Component\Messenger\MessageBusInterface;

use App\Core\Ports\Gateways\External\Search\ElasticsearchGatewayContract;

abstract class AbstractIndexSubscriber
{
    /** @var array<int, int> */
    private array $pendingRemoveIds = [];

    public function __construct(
        protected readonly ElasticsearchGatewayContract $elasticsearch,
        protected readonly MessageBusInterface $bus,
    ) {}

    /** @return class-string */
    abstract protected function getEntityClass(): string;

    abstract protected function createIndexMessage(int $id): object;
    abstract protected function createRemoveMessage(int $id): object;

    /** @param LifecycleEventArgs<EntityManagerInterface> $args */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->dispatchIndex($args->getObject());
    }

    /** @param LifecycleEventArgs<EntityManagerInterface> $args */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->dispatchIndex($args->getObject());
    }

    /** @param LifecycleEventArgs<EntityManagerInterface> $args */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $class = $this->getEntityClass();

        if (!$entity instanceof $class || !$this->elasticsearch->isEnabled()) {
            return;
        }

        assert(method_exists($entity, 'getId'));

        /** @var int $entityId */
        $entityId = $entity->getId();

        $this->pendingRemoveIds[spl_object_id($entity)] = $entityId;
    }

    /** @param LifecycleEventArgs<EntityManagerInterface> $args */
    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $class = $this->getEntityClass();

        if (!$entity instanceof $class || !$this->elasticsearch->isEnabled()) {
            return;
        }

        $objectId = spl_object_id($entity);

        if (!isset($this->pendingRemoveIds[$objectId])) {
            return;
        }

        $id = $this->pendingRemoveIds[$objectId];
        unset($this->pendingRemoveIds[$objectId]);

        $this->bus->dispatch($this->createRemoveMessage($id));
    }

    private function dispatchIndex(object $entity): void
    {
        $class = $this->getEntityClass();

        if (!$entity instanceof $class || !$this->elasticsearch->isEnabled()) {
            return;
        }

        assert(method_exists($entity, 'getId'));

        /** @var int $id */
        $id = $entity->getId();

        $this->bus->dispatch($this->createIndexMessage($id));
    }
}
