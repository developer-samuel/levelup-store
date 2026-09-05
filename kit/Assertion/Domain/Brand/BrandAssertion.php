<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\Brand;

use Kit\Assertion\Shared\EntityAssertion;

use App\Core\Domain\Segment\Brand\Brand;

final readonly class BrandAssertion
{
    /**
     * @param Brand|null $brand
     * @param string $identifier
     *
     * @return Brand
     *
     * @throws \RuntimeException
    */
    public static function assertExistsWithIdentifier(?Brand $brand, string $identifier): Brand
    {
        return EntityAssertion::assertExists($brand, $identifier, Brand::class);
    }
}
