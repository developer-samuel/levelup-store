<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Brand\Service;

use Kit\{
    Assertion\Shared\EntityAssertion,
    Utils\Shared\DataSanitizer
};

use App\Core\Domain\{
    Admin\Brand\AdminBrandPayload,
    Segment\Brand\Brand
};

use App\Core\Ports\{
    Admin\Segment\Brand\Service\AdminBrandCommandContract,
    Segment\Brand\BrandRepositoryContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class AdminBrandCommandService implements AdminBrandCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param BrandRepositoryContract $brandRepository
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private BrandRepositoryContract $brandRepository,
    ) {}

    /**
     * @param AdminBrandPayload $payload
     *
     * @return Brand
    */
    public function createBrand(AdminBrandPayload $payload): Brand
    {
        $brand = (new Brand())
            ->setName($payload->name);

        $this->entityPersistence->persist($brand, true);

        return $brand;
    }

    /**
     * @param int $id
     * @param AdminBrandPayload $payload
     *
     * @return Brand
    */
    public function updateBrand(int $id, AdminBrandPayload $payload): Brand
    {
        $brand = EntityAssertion::assertExists(
            $this->brandRepository->findById($id),
            $id,
            Brand::class,
        );

        $brand->setName($payload->name)
            ->setUpdatedAt();

        $this->entityPersistence->persist($brand, true);

        return $brand;
    }

    /**
     * @param Brand $brand
     *
     * @return void
    */
    public function destroyBrand(Brand $brand): void
    {
        $this->entityPersistence->remove($brand, true);
    }

    /**
     * @param AdminBrandPayload $payload
     *
     * @return int
     *
     * @throws \InvalidArgumentException
    */
    public function validateId(AdminBrandPayload $payload): int
    {
        $id = DataSanitizer::sanitizeInt($payload->id);
        if ($id === null || $id <= 0) {
            throw new \InvalidArgumentException('Invalid ID.');
        }

        return $id;
    }
}
