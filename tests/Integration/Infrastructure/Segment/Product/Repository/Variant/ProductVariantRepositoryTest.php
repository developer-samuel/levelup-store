<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Segment\Product\Repository\Variant;

use Doctrine\{
    ORM\EntityManagerInterface,
    Persistence\ManagerRegistry
};

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\Core\Domain\{
    Segment\Brand\Entity\Brand,
    Segment\Category\Entity\Category,
    Segment\Product\Entity\Product,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\Enum\ProductSortOption,
    Segment\Product\ValueObject\ProductFilterObject,
    Segment\Type\Entity\Type
};

use App\Core\Ports\{
    Gateways\External\Search\ElasticsearchGatewayContract,
    Segment\Product\Projection\ProductVariantProjectionQueryContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract
};

use App\Infrastructure\Segment\Product\Repository\Variant\ProductVariantRepository;

use Tests\Support\Provides\Persistence;

/**
 * @coversDefaultClass \App\Infrastructure\Segment\Product\Repository\Variant\ProductVariantRepository
*/
class ProductVariantRepositoryTest extends KernelTestCase
{
    use Persistence;

    private EntityManagerInterface $em;
    private ProductVariantRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = $this->getEntityManager();

        $elasticsearch = $this->createMock(ElasticsearchGatewayContract::class);
        $elasticsearch->method('isEnabled')->willReturn(false);

        $projectionQuery = $this->createMock(ProductVariantProjectionQueryContract::class);

        /** @var ManagerRegistry $registry */
        $registry = static::getContainer()->get('doctrine');

        $this->repository = new ProductVariantRepository($elasticsearch, $projectionQuery, $registry);

        $this->em->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->rollback();

        parent::tearDown();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(ProductVariantRepositoryContract::class, $this->repository);
    }

    public function testFindByIdReturnsVariantWhenFound(): void
    {
        $variant = $this->createAndPersistVariant('SKU-BYID-001', 'Variant ById Test', 'variant-byid-test');

        $result = $this->repository->findById($variant->getId());

        $this->assertInstanceOf(ProductVariant::class, $result);
        $this->assertSame($variant->getId(), $result->getId());
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findById(999999);

        $this->assertNull($result);
    }

    public function testFindOneByUrlReturnsVariantWhenFound(): void
    {
        $this->createAndPersistVariant('SKU-URL-001', 'Variant Url Test', 'variant-url-test');

        $result = $this->repository->findOneByUrl('variant-url-test');

        $this->assertInstanceOf(ProductVariant::class, $result);
        $this->assertSame('variant-url-test', $result->getUrl());
    }

    public function testFindOneByUrlIsCaseInsensitive(): void
    {
        $this->createAndPersistVariant('SKU-URLCASE-001', 'Variant Url Case', 'variant-url-case');

        $result = $this->repository->findOneByUrl('VARIANT-URL-CASE');

        $this->assertInstanceOf(ProductVariant::class, $result);
    }

    public function testFindOneByUrlReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findOneByUrl('nonexistent-url');

        $this->assertNull($result);
    }

    public function testSearchByNameReturnsMatchingVariants(): void
    {
        $unique = uniqid('UniqueTestVariant');
        $this->createAndPersistVariant('SKU-SEARCH-001', $unique, strtolower($unique));

        $results = $this->repository->searchByName($unique);

        $this->assertNotEmpty($results);
        $this->assertContainsOnlyInstancesOf(ProductVariant::class, $results);
    }

    public function testSearchByNameReturnsEmptyWhenNoMatch(): void
    {
        $results = $this->repository->searchByName('zzz-absolutely-no-match-xyz-999');

        $this->assertEmpty($results);
    }

    public function testFindAllByProductReturnsVariantsForProduct(): void
    {
        $variant = $this->createAndPersistVariant('SKU-BYPROD-001', 'Variant ByProduct Test', 'variant-byprod-test');
        $product = $variant->getProduct();

        $results = $this->repository->findAllByProduct($product);

        $this->assertNotEmpty($results);
        $this->assertContainsOnlyInstancesOf(ProductVariant::class, $results);
    }

    public function testFindAvailableVariantsPaginatedReturnsArray(): void
    {
        $filter = $this->defaultFilter();

        $result = $this->repository->findAvailableVariantsPaginated($filter, 1, 12, ProductSortOption::TOP_RATED);

        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertIsArray($result['items']);
    }

    public function testFindAllReturnsArrayOfVariants(): void
    {
        $this->createAndPersistVariant('SKU-ALL-001', 'Variant All', 'variant-all-001');

        $result = $this->repository->findAll();

        $this->assertNotEmpty($result);
        $this->assertContainsOnlyInstancesOf(ProductVariant::class, $result);
    }

    public function testFindAvailableVariantsPaginatedWithBrandFilter(): void
    {
        $variant = $this->createAndPersistVariant('SKU-BRAND-001', 'Variant Brand', 'variant-brand-001');
        $brandSlug = $this->toSlug($variant->getProduct()->getBrand()->getName());

        $filter = new ProductFilterObject(
            isDiscountRoute: false,
            subtypes:        [],
            brands:          [$brandSlug],
        );

        $result = $this->repository->findAvailableVariantsPaginated($filter, 1, 12);

        $this->assertArrayHasKey('items', $result);
        $this->assertNotEmpty($result['items']);
    }

    public function testFindAvailableVariantsPaginatedWithBrandFilterHyphenatedName(): void
    {
        [$name, $slug, $suffix] = $this->uniqueBrand('Inter-Tech');

        $this->createAndPersistVariantWithBrand('SKU-INTER-TECH-' . $suffix, 'variant-inter-tech-' . $suffix, $name);

        $result = $this->repository->findAvailableVariantsPaginated(
            new ProductFilterObject(isDiscountRoute: false, subtypes: [], brands: [$slug]),
            1, 12,
        );

        $this->assertArrayHasKey('items', $result);
        $this->assertNotEmpty($result['items']);
    }

    public function testFindAvailableVariantsPaginatedWithBrandFilterNoHyphenInName(): void
    {
        [$name, $slug, $suffix] = $this->uniqueBrand('MSI');

        $this->createAndPersistVariantWithBrand('SKU-MSI-' . $suffix, 'variant-msi-' . $suffix, $name);

        $result = $this->repository->findAvailableVariantsPaginated(
            new ProductFilterObject(isDiscountRoute: false, subtypes: [], brands: [$slug]),
            1, 12,
        );

        $this->assertArrayHasKey('items', $result);
        $this->assertNotEmpty($result['items']);
    }

    public function testFindAvailableVariantsPaginatedWithCategoryFilter(): void
    {
        $variant = $this->createAndPersistVariant('SKU-CAT-001', 'Variant Category', 'variant-cat-001');
        $categorySlug = $this->toSlug($variant->getProduct()->getCategory()->getName());

        $filter = new ProductFilterObject(
            isDiscountRoute: false,
            subtypes:        [],
            brands:          [],
            category:        $categorySlug,
        );

        $result = $this->repository->findAvailableVariantsPaginated($filter, 1, 12);

        $this->assertArrayHasKey('items', $result);
        $this->assertNotEmpty($result['items']);
    }

    public function testFindAvailableVariantsPaginatedWithTypeFilter(): void
    {
        $variant = $this->createAndPersistVariant('SKU-TYPE-F-001', 'Variant Type Filter', 'variant-type-f-001');
        $typeSlug = $this->toSlug($variant->getProduct()->getType()->getName());

        $filter = new ProductFilterObject(
            isDiscountRoute: false,
            subtypes:        [],
            brands:          [],
            type:            $typeSlug,
        );

        $result = $this->repository->findAvailableVariantsPaginated($filter, 1, 12);

        $this->assertArrayHasKey('items', $result);
        $this->assertNotEmpty($result['items']);
    }

    public function testFindAvailableVariantsPaginatedWithPriceRangeFilter(): void
    {
        $this->createAndPersistVariant('SKU-PRICE-001', 'Variant Price', 'variant-price-001');

        $filter = new ProductFilterObject(
            isDiscountRoute: false,
            subtypes:        [],
            brands:          [],
            minPrice:        0.01,
            maxPrice:        999.99,
        );

        $result = $this->repository->findAvailableVariantsPaginated($filter, 1, 12);

        $this->assertArrayHasKey('items', $result);
        $this->assertIsArray($result['items']);
    }

    public function testFindAvailableVariantsPaginatedWithDiscountRoute(): void
    {
        $filter = new ProductFilterObject(
            isDiscountRoute: true,
            subtypes:        [],
            brands:          [],
        );

        $result = $this->repository->findAvailableVariantsPaginated($filter, 1, 12);

        $this->assertArrayHasKey('items', $result);
        $this->assertIsArray($result['items']);
    }

    public function testFindAvailableVariantsPaginatedWithSubtypeFilter(): void
    {
        $filter = new ProductFilterObject(
            isDiscountRoute: false,
            subtypes:        ['gaming'],
            brands:          [],
        );

        $result = $this->repository->findAvailableVariantsPaginated($filter, 1, 12);

        $this->assertArrayHasKey('items', $result);
        $this->assertIsArray($result['items']);
    }

    public function testGetMaxPriceForFilterReturnsFloat(): void
    {
        $filter = $this->defaultFilter();

        $result = $this->repository->getMaxPriceForFilter($filter);

        $this->assertGreaterThanOrEqual(0.0, $result);
    }

    public function testFindAvailableVariantsPaginatedReturnsMultipleItemsWhenPresent(): void
    {
        $this->createAndPersistVariant('SKU-MULTI-001', 'Multi Variant 1', 'multi-variant-001');
        $this->createAndPersistVariant('SKU-MULTI-002', 'Multi Variant 2', 'multi-variant-002');
        $this->createAndPersistVariant('SKU-MULTI-003', 'Multi Variant 3', 'multi-variant-003');

        $filter = $this->defaultFilter();

        $result = $this->repository->findAvailableVariantsPaginated($filter, 1, 100);

        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertGreaterThanOrEqual(3, count($result['items']));
    }

    public function testFindAvailableVariantsPaginatedUsesDefaultSortWhenNullPassed(): void
    {
        $this->createAndPersistVariant('SKU-SORT-001', 'Sort Default Variant', 'sort-default-001');

        $filter = $this->defaultFilter();

        $result = $this->repository->findAvailableVariantsPaginated($filter, 1, 12);

        $this->assertArrayHasKey('items', $result);
        $this->assertIsArray($result['items']);
    }

    public function testFindAvailableVariantsPaginatedSortByCheapest(): void
    {
        $filter = $this->defaultFilter();

        $result = $this->repository->findAvailableVariantsPaginated($filter, 1, 12, ProductSortOption::CHEAPEST);

        $this->assertArrayHasKey('items', $result);
        $this->assertIsArray($result['items']);
    }

    public function testFindAvailableVariantsPaginatedSortByMostExpensive(): void
    {
        $filter = $this->defaultFilter();

        $result = $this->repository->findAvailableVariantsPaginated($filter, 1, 12, ProductSortOption::MOST_EXPENSIVE);

        $this->assertArrayHasKey('items', $result);
        $this->assertIsArray($result['items']);
    }

    public function testFindAvailableVariantsPaginatedSortByLatest(): void
    {
        $filter = $this->defaultFilter();

        $result = $this->repository->findAvailableVariantsPaginated($filter, 1, 12, ProductSortOption::LATEST);

        $this->assertArrayHasKey('items', $result);
        $this->assertIsArray($result['items']);
    }

    public function testFindAvailableVariantsPaginatedPageTwoReturnsOffset(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->createAndPersistVariant('SKU-PAGE-00' . $i, 'Page Variant ' . $i, 'page-variant-00' . $i);
        }

        $filter = $this->defaultFilter();

        $page1 = $this->repository->findAvailableVariantsPaginated($filter, 1, 2);
        $page2 = $this->repository->findAvailableVariantsPaginated($filter, 2, 2);

        $this->assertArrayHasKey('items', $page1);
        $this->assertArrayHasKey('items', $page2);
    }

    public function testFindOneByUrlReturnsNullWhenVariantHasNoStock(): void
    {
        $result = $this->repository->findOneByUrl('url-that-does-not-exist-xyz');

        $this->assertNull($result);
    }

    public function testFindRandomAvailableExcludingReturnsAvailableVariant(): void
    {
        $this->createAndPersistVariant('SKU-RAND-001', 'Random Variant', 'random-variant-001');

        $result = $this->repository->findRandomAvailableExcluding([]);

        $this->assertInstanceOf(ProductVariant::class, $result);
    }

    public function testFindRandomAvailableExcludingReturnsNullWhenAllExcluded(): void
    {
        $variant = $this->createAndPersistVariant('SKU-RAND-EX-001', 'Excluded Variant', 'rand-excluded-001');

        $this->em->clear();

        $fresh = $this->repository->findById($variant->getId());

        $this->assertNotNull($fresh);

        $allVariants = $this->repository->findAll();
        $allIds = array_map(fn(ProductVariant $v) => $v->getId(), $allVariants);

        $result = $this->repository->findRandomAvailableExcluding($allIds);

        $this->assertNull($result);
    }

    public function testFindAvailableVariantsPaginatedUsesElasticsearchWhenEnabled(): void
    {
        $variant = $this->createAndPersistVariant('SKU-ES-PAG-001', 'ES Paginated Variant', 'es-paginated-001');
        $filter  = $this->defaultFilter();

        $repository = $this->getRepositoryWithElasticsearch(
            filterResult: ['ids' => [$variant->getId()], 'total' => 1],
            searchResult: ['ids' => []],
        );

        $result = $repository->findAvailableVariantsPaginated($filter, 1, 12);

        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertCount(1, $result['items']);
        $this->assertSame(1, $result['total']);
    }

    public function testFindAvailableVariantsPaginatedReturnsEmptyWhenElasticsearchReturnsNoIds(): void
    {
        $filter = $this->defaultFilter();

        $repository = $this->getRepositoryWithElasticsearch(
            filterResult: ['ids' => [], 'total' => 0],
            searchResult: ['ids' => []],
        );

        $result = $repository->findAvailableVariantsPaginated($filter, 1, 12);

        $this->assertSame([], $result['items']);
        $this->assertSame(0, $result['total']);
    }

    public function testSearchByNameUsesElasticsearchWhenEnabled(): void
    {
        $variant = $this->createAndPersistVariant('SKU-ES-SEARCH-001', 'ES Search Variant', 'es-search-001');

        $repository = $this->getRepositoryWithElasticsearch(
            filterResult: ['ids' => [], 'total' => 0],
            searchResult: ['ids' => [$variant->getId()]],
        );

        $results = $repository->searchByName('ES Search');

        $this->assertNotEmpty($results);
        $this->assertContainsOnlyInstancesOf(ProductVariant::class, $results);
    }

    public function testSearchByNameReturnsEmptyWhenElasticsearchReturnsNoIds(): void
    {
        $repository = $this->getRepositoryWithElasticsearch(
            filterResult: ['ids' => [], 'total' => 0],
            searchResult: ['ids' => []],
        );

        $results = $repository->searchByName('anything');

        $this->assertSame([], $results);
    }

    public function testFindAvailableVariantsPaginatedSkipsNonExistentIdsFromElasticsearch(): void
    {
        $variant = $this->createAndPersistVariant('SKU-ES-SKIP-001', 'ES Skip Variant', 'es-skip-001');
        $filter  = $this->defaultFilter();

        $repository = $this->getRepositoryWithElasticsearch(
            filterResult: ['ids' => [999999, $variant->getId()], 'total' => 2],
            searchResult: ['ids' => []],
        );

        $result = $repository->findAvailableVariantsPaginated($filter, 1, 12);

        $this->assertCount(1, $result['items']);
        $this->assertSame($variant->getId(), $result['items'][0]->getId());
    }

    public function testFindRandomAvailableExcludingRespectsExcludedIds(): void
    {
        $this->createAndPersistVariant('SKU-RAND-A', 'Random A', 'random-a');
        $this->createAndPersistVariant('SKU-RAND-B', 'Random B', 'random-b');

        $this->em->clear();

        $idA = $this->repository->findOneByUrl('random-a')?->getId();
        $idB = $this->repository->findOneByUrl('random-b')?->getId();

        $this->assertNotNull($idA);
        $this->assertNotNull($idB);

        $result = $this->repository->findRandomAvailableExcluding([$idA]);

        if ($result !== null) {
            $this->assertNotSame($idA, $result->getId());
        }
    }

    private function toSlug(string $name): string
    {
        return strtolower(str_replace(' ', '-', $name));
    }

    /**
     * @return array{
     *     0: string,
     *     1: string,
     *     2: string
     * }
    */
    private function uniqueBrand(string $prefix): array
    {
        $suffix = substr(uniqid('', true), 0, 6);
        $name = $prefix . '-' . $suffix;

        return [$name, $this->toSlug($name), $suffix];
    }

    private function defaultFilter(): ProductFilterObject
    {
        return new ProductFilterObject(
            isDiscountRoute: false,
            subtypes:        [],
            brands:          [],
        );
    }

    private function createAndPersistVariant(string $sku, string $name, string $url): ProductVariant
    {
        $product = $this->createAndPersistProduct();

        $variant = (new ProductVariant())
            ->setProduct($product)
            ->setSku($sku)
            ->setName($name)
            ->setUrl($url)
            ->setPrice(99.99);

        $stock = (new ProductVariantStock())
            ->setVariant($variant)
            ->setQuantityAvailable(10);

        $ean = (new ProductVariantEan())
            ->setVariant($variant)
            ->setCode(substr(md5(uniqid('', true)), 0, 13));

        $this->em->persist($variant);
        $this->em->persist($stock);
        $this->em->persist($ean);
        $this->em->flush();
        $this->em->clear();

        return $variant;
    }

    private function createAndPersistVariantWithBrand(string $sku, string $url, string $brandName): ProductVariant
    {
        $category = (new Category())->setName(substr(md5(uniqid('', true)), 0, 20));
        $type = (new Type())->setName('Type ' . uniqid('', true))->setCategory($category);
        $brand = (new Brand())->setName($brandName);

        $this->em->persist($category);
        $this->em->persist($type);
        $this->em->persist($brand);

        $product = (new Product())
            ->setName('Product ' . uniqid('', true))
            ->setCategory($category)
            ->setType($type)
            ->setBrand($brand)
            ->setCatalogCode('CAT-' . substr(uniqid('', true), 0, 45));

        $this->em->persist($product);

        $variant = (new ProductVariant())
            ->setProduct($product)
            ->setSku($sku)
            ->setName('Variant ' . $brandName)
            ->setUrl($url)
            ->setPrice(99.99);

        $stock = (new ProductVariantStock())
            ->setVariant($variant)
            ->setQuantityAvailable(10);

        $ean = (new ProductVariantEan())
            ->setVariant($variant)
            ->setCode(substr(md5(uniqid('', true)), 0, 13));

        $this->em->persist($variant);
        $this->em->persist($stock);
        $this->em->persist($ean);
        $this->em->flush();
        $this->em->clear();

        return $variant;
    }

    private function createAndPersistProduct(): Product
    {
        $category = (new Category())->setName(substr(md5(uniqid('', true)), 0, 20));
        $type = (new Type())->setName('Type ' . uniqid('', true))->setCategory($category);
        $brand = (new Brand())->setName('Brand ' . uniqid('', true));

        $this->em->persist($category);
        $this->em->persist($type);
        $this->em->persist($brand);

        $product = (new Product())
            ->setName('Product ' . uniqid('', true))
            ->setCategory($category)
            ->setType($type)
            ->setBrand($brand)
            ->setCatalogCode('CAT-' . substr(uniqid('', true), 0, 45));

        $this->em->persist($product);

        return $product;
    }

    /**
     * @param array{ids: int[], total: int} $filterResult
     * @param array{ids: int[]} $searchResult
    */
    private function getRepositoryWithElasticsearch(array $filterResult, array $searchResult): ProductVariantRepository
    {
        $elasticsearch = $this->createMock(ElasticsearchGatewayContract::class);
        $elasticsearch->method('isEnabled')->willReturn(true);

        $projectionQuery = $this->createMock(ProductVariantProjectionQueryContract::class);
        $projectionQuery->method('filter')->willReturn($filterResult);
        $projectionQuery->method('search')->willReturn($searchResult);

        /** @var ManagerRegistry $registry */
        $registry = static::getContainer()->get('doctrine');

        return new ProductVariantRepository($elasticsearch, $projectionQuery, $registry);
    }
}
