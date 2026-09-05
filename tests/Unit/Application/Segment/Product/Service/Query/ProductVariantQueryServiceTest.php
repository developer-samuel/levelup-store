<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Product\Service\Query;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Product\Entity\Product,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\ValueObject\ProductVariantObject
};

use App\Core\Application\Segment\Product\Service\Query\ProductVariantQueryService;

use App\Core\Ports\{
    Segment\Product\Assembler\ProductVariantAssemblerContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Segment\Product\Service\Query\ProductVariantQueryContract,
    Segment\Review\Service\Query\ReviewQueryContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Product\Service\Query\ProductVariantQueryService
*/
final class ProductVariantQueryServiceTest extends TestCase
{
    private ReviewQueryContract&MockObject $reviewQuery;
    private ProductVariantRepositoryContract&MockObject $variantRepository;
    private ProductVariantAssemblerContract&MockObject $assembler;
    private ProductVariantQueryService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(ProductVariantQueryContract::class, $this->service);
    }

    public function testGetVariantOrNullReturnsVariantWhenFound(): void
    {
        $variant = $this->createMock(ProductVariant::class);

        $this->variantRepository
            ->method('findOneByUrl')
            ->with('test-product-url')
            ->willReturn($variant);

        $result = $this->service->getVariantOrNull('test-product-url');

        $this->assertSame($variant, $result);
    }

    public function testGetVariantOrNullReturnsNullWhenNotFound(): void
    {
        $this->variantRepository
            ->method('findOneByUrl')
            ->with('non-existing-url')
            ->willReturn(null);

        $result = $this->service->getVariantOrNull('non-existing-url');

        $this->assertNull($result);
    }

    public function testGetAllVariantsOrNullReturnsVariants(): void
    {
        $variantA = $this->createMock(ProductVariant::class);
        $variantB = $this->createMock(ProductVariant::class);

        $this->variantRepository->method('findAllByProduct')->willReturn([$variantA, $variantB]);

        $result = $this->service->getAllVariantsOrNull($this->buildVariantWithProduct());

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(ProductVariant::class, $result);
    }

    public function testGetAllVariantsOrNullReturnsSequentialKeys(): void
    {
        $this->variantRepository->method('findAllByProduct')->willReturn([
            5  => $this->createMock(ProductVariant::class),
            10 => $this->createMock(ProductVariant::class),
        ]);

        $result = $this->service->getAllVariantsOrNull($this->buildVariantWithProduct());

        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertArrayNotHasKey(5, $result);
        $this->assertArrayNotHasKey(10, $result);
    }

    public function testGetAllVariantsOrNullReturnsEmptyArrayWhenNoVariants(): void
    {
        $this->variantRepository->method('findAllByProduct')->willReturn([]);

        $result = $this->service->getAllVariantsOrNull($this->buildVariantWithProduct());

        $this->assertEmpty($result);
    }

    public function testMapVariantsToDataReturnsProductVariantObjects(): void
    {
        $variant = $this->createMock(ProductVariant::class);
        $object = $this->buildVariantObject();

        $this->assembler
            ->method('toObject')
            ->with($variant, $this->reviewQuery)
            ->willReturn($object);

        $result = $this->service->mapVariantsToData([$variant]);

        $this->assertCount(1, $result);
        $this->assertContainsOnlyInstancesOf(ProductVariantObject::class, $result);
    }

    public function testMapVariantsToDataReturnsEmptyArrayOnEmptyInput(): void
    {
        $this->assembler->expects($this->never())->method('toObject');

        $result = $this->service->mapVariantsToData([]);

        $this->assertEmpty($result);
    }

    public function testMapVariantsToDataCallsAssemblerForEachVariant(): void
    {
        $variantA = $this->createMock(ProductVariant::class);
        $variantB = $this->createMock(ProductVariant::class);

        $this->assembler
            ->expects($this->exactly(2))
            ->method('toObject')
            ->willReturn($this->buildVariantObject());

        $result = $this->service->mapVariantsToData([$variantA, $variantB]);

        $this->assertCount(2, $result);
    }

    private function initMocks(): void
    {
        $this->reviewQuery = $this->createMock(ReviewQueryContract::class);
        $this->variantRepository = $this->createMock(ProductVariantRepositoryContract::class);
        $this->assembler = $this->createMock(ProductVariantAssemblerContract::class);
    }

    private function initService(): void
    {
        $this->service = new ProductVariantQueryService(
            $this->reviewQuery,
            $this->variantRepository,
            $this->assembler,
        );
    }

    private function buildVariantWithProduct(): ProductVariant&MockObject
    {
        $product = $this->createMock(Product::class);
        $variant = $this->createMock(ProductVariant::class);
        $variant->method('getProduct')->willReturn($product);

        return $variant;
    }

    private function buildVariantObject(): ProductVariantObject
    {
        return new ProductVariantObject(
            variantId:     1,
            price:         99.99,
            discountPrice: 89.99,
            imagePath:     '/images/product.jpg',
            name:          'Test Variant',
            url:           'test-variant-url',
            createdAt:     '2024-01-01',
        );
    }
}
