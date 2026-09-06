<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Brand\Service;

use Kit\Assertion\Domain\Brand\BrandAssertion;

use App\Core\Domain\Segment\Brand\Brand;

use App\Core\Ports\{
    Segment\Brand\Service\BrandQueryContract,
    Segment\Brand\BrandRepositoryContract
};

final readonly class BrandQueryService implements BrandQueryContract
{
    /**
     * @param BrandRepositoryContract $brandRepository
    */
    public function __construct(
        private BrandRepositoryContract $brandRepository,
    ) {}

    /**
     * @param int $id
     *
     * @return Brand
    */
    public function getBrandByIdOrFail(int $id): Brand
    {
        $brand = $this->brandRepository->findById($id);

        return BrandAssertion::assertExistsWithIdentifier(
            $brand,
            'Brand ID ' . $id,
        );
    }
}
