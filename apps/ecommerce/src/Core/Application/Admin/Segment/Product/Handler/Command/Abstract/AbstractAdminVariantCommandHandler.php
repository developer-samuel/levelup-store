<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Product\Handler\Command\Abstract;

use App\Core\Application\Admin\Abstract\AbstractAdminFormCommandHandler;

use App\Core\Ports\{
    Admin\Segment\Product\Service\Command\AdminVariantValidationCommandContract,
    Security\SecurityPolicyContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

abstract class AbstractAdminVariantCommandHandler extends AbstractAdminFormCommandHandler
{
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

    abstract protected function getPayloadClass(): string;
    abstract protected function getEntityName(): string;
    abstract protected function getEntityOrFail(int $id): object;
    abstract protected function createEntity(int $variantId, object $payload): void;
    abstract protected function updateEntity(int $id, int $variantId, object $payload): void;
    abstract protected function destroyEntity(object $entity): void;

    protected function assertPayloadType(object $payload): void
    {
        $expectedClass = $this->getPayloadClass();

        if (!$payload instanceof $expectedClass) {
            throw new \LogicException(
                sprintf('Invalid payload type. Expected %s, got %s.', $expectedClass, get_class($payload)),
            );
        }
    }

    /** @return array<string, mixed> */
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

    /** @return array<string, mixed> */
    public function handleDestroy(int $id): array
    {
        return $this->execute(function() use ($id) {
            $entity = $this->getEntityOrFail($id);
            $this->destroyEntity($entity);

            return ApiResultFormatter::success($this->formatMessage('deleted'));
        });
    }

    private function formatMessage(string $action): string
    {
        return sprintf('%s %s successfully.', $this->getEntityName(), $action);
    }
}
