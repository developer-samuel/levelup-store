<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Product\Handler\Command\Variant\Abstract;

use App\Core\Application\Admin\Abstract\AbstractAdminFormCommandHandler;

use App\Core\Ports\{
    Admin\Segment\Product\Service\Command\Variant\AdminVariantValidationCommandContract,
    Security\Policy\SecurityPolicyContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

abstract class AbstractAdminVariantCommandHandler extends AbstractAdminFormCommandHandler
{
    /**
     * @param AdminVariantValidationCommandContract $adminVariantValidationCommand
     * @param SecurityPolicyContract $securityPolicy
     * @param AppLoggerContract $logger
    */
    public function __construct(
        protected readonly AdminVariantValidationCommandContract $adminVariantValidationCommand,
        SecurityPolicyContract $securityPolicy,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $securityPolicy,
            $logger,
        );
    }

    /**
     * @return string
    */
    abstract protected function getPayloadClass(): string;

    /**
     * @return string
    */
    abstract protected function getEntityName(): string;

    /**
     * @param int $id
     *
     * @return object
    */
    abstract protected function getEntityOrFail(int $id): object;

    /**
     * @param int $variantId
     * @param object $payload
     *
     * @return void
    */
    abstract protected function createEntity(int $variantId, object $payload): void;

    /**
     * @param int $id
     * @param int $variantId
     * @param object $payload
     *
     * @return void
    */
    abstract protected function updateEntity(int $id, int $variantId, object $payload): void;

    /**
     * @param object $entity
     *
     * @return void
    */
    abstract protected function destroyEntity(object $entity): void;

    /**
     * @param object $payload
     *
     * @return void
     *
     * @throws \LogicException
    */
    protected function assertPayloadType(object $payload): void
    {
        $expectedClass = $this->getPayloadClass();

        if (!$payload instanceof $expectedClass) {
            throw new \LogicException(
                sprintf('Invalid payload type. Expected %s, got %s.', $expectedClass, get_class($payload)),
            );
        }
    }

    /**
     * @param object $payload
     *
     * @return array<string, mixed>
    */
    public function handleCreate(object $payload): array
    {
        return $this->executeAdmin(function() use ($payload) {
            $variantId = $this->adminVariantValidationCommand->extractAndValidateVariantId($payload);

            $this->assertPayloadType($payload);
            $this->createEntity($variantId, $payload);

            return ApiResultFormatter::success($this->formatMessage('created'));
        });
    }

    /**
     * @param object $payload
     *
     * @return array<string, mixed>
    */
    public function handleUpdate(object $payload): array
    {
        return $this->execute(function() use ($payload) {
            $id = $this->adminVariantValidationCommand->extractAndValidateId($payload);
            $variantId = $this->adminVariantValidationCommand->extractAndValidateVariantId($payload);

            $this->assertPayloadType($payload);
            $this->updateEntity($id, $variantId, $payload);

            return ApiResultFormatter::success($this->formatMessage('updated'));
        });
    }

    /**
     * @param int $id
     *
     * @return array<string, mixed>
    */
    public function handleDestroy(int $id): array
    {
        return $this->execute(function() use ($id) {
            $entity = $this->getEntityOrFail($id);
            $this->destroyEntity($entity);

            return ApiResultFormatter::success($this->formatMessage('deleted'));
        });
    }

    /**
     * @param string $action
     *
     * @return string
    */
    private function formatMessage(string $action): string
    {
        return sprintf('%s %s successfully.', $this->getEntityName(), $action);
    }
}
