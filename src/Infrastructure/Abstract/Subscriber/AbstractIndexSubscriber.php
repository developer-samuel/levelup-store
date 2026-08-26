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
    /**
     * @param ElasticsearchGatewayContract $elasticsearch
     * @param MessageBusInterface $bus
    */
    public function __construct(
        protected readonly ElasticsearchGatewayContract $elasticsearch,
        protected readonly MessageBusInterface $bus,
    ) {}

    /**
     * @return class-string
    */
    abstract protected function getEntityClass(): string;

    /**
     * @param int $id
     *
     * @return object
    */
    abstract protected function createIndexMessage(int $id): object;

    /**
     * @param int $id
     *
     * @return object
    */
    abstract protected function createRemoveMessage(int $id): object;

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $args
     *
     * @return void
    */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->dispatchIndex($args->getObject());
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $args
     *
     * @return void
    */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->dispatchIndex($args->getObject());
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $args
     *
     * @return void
    */
    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $class  = $this->getEntityClass();

        if (!$entity instanceof $class || !$this->elasticsearch->isEnabled()) {
            return;
        }

        assert(method_exists($entity, 'getId'));

        /** @var int $id */
        $id = $entity->getId();

        $this->bus->dispatch($this->createRemoveMessage($id));
    }

    /**
     * @param object $entity
     *
     * @return void
    */
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
