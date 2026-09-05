<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\Segment\Product\Controller\Query;

use Symfony\{
    Bundle\FrameworkBundle\KernelBrowser,
    Bundle\FrameworkBundle\Test\WebTestCase,
    Component\HttpFoundation\Response
};

use PHPUnit\Framework\MockObject\MockObject;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\ValueObject\ProductDetailObject,
    Segment\Product\ValueObject\ProductListObject,
    Segment\Product\ValueObject\ProductPriceObject
};

use App\Core\Ports\Segment\Product\{
    Handler\Query\ProductDetailQueryHandlerContract,
    Handler\Query\ProductQueryHandlerContract,
    Renderer\ProductRendererContract
};

/**
 * @coversDefaultClass \App\Presentation\Segment\Product\Controller\ProductQueryController
*/
class ProductQueryControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        static::getContainer()->set(ProductRendererContract::class, $this->createRendererStub());
    }

    public function testIndexReturnsSuccessfulResponse(): void
    {
        $this->client->request('GET', '/products');

        $this->assertResponseIsSuccessful();
    }

    public function testIndexWithCategoryReturnsSuccessfulResponse(): void
    {
        $this->client->request('GET', '/products/electronics');

        $this->assertResponseIsSuccessful();
    }

    public function testIndexWithCategoryAndTypeReturnsSuccessfulResponse(): void
    {
        $this->client->request('GET', '/products/electronics/smartphones');

        $this->assertResponseIsSuccessful();
    }

    public function testIndexAjaxRequestReturnsSuccessfulResponse(): void
    {
        $this->client->request('GET', '/products', [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testIndexRedirectsWhenPageExceedsTotalResults(): void
    {
        $this->client->request('GET', '/products', ['page' => '3']);

        $this->assertResponseRedirects();
    }

    public function testIndexWithExplicitSortReturnsSuccessfulResponse(): void
    {
        $this->client->request('GET', '/products', ['sort' => 'cheapest']);

        $this->assertResponseIsSuccessful();
    }

    public function testIndexWithBrandStringParamRedirects(): void
    {
        $this->client->request('GET', '/products', ['brand' => 'nike']);

        $this->assertResponseRedirects();
    }

    public function testIndexWithNoPaginationInHandlerResultReturnsSuccessfulResponse(): void
    {
        $handler = $this->createMock(ProductQueryHandlerContract::class);
        $handler->method('handle')->willReturn([]);

        static::getContainer()->set(ProductQueryHandlerContract::class, $handler);

        $this->client->request('GET', '/products');

        $this->assertResponseIsSuccessful();
    }

    public function testShowRedirectsWhenVariantNotFound(): void
    {
        $this->client->request('GET', '/product/show/nonexistent-url');

        $this->assertResponseRedirects();
    }

    public function testShowReturnsDetailWhenVariantFound(): void
    {
        static::getContainer()->set(ProductDetailQueryHandlerContract::class, $this->createDetailHandlerMock());

        $this->client->request('GET', '/product/show/existing-product-url');

        $this->assertResponseIsSuccessful();
    }

    private function createRendererStub(): ProductRendererContract
    {
        return new class implements ProductRendererContract {
            /**
             * @param array<string, mixed> $data
            */
            public function renderProducts(array $data): Response
            {
                return new Response('<html>products</html>');
            }

            public function renderProductsList(ProductListObject $data): Response
            {
                return new Response('<ul>list</ul>');
            }

            public function renderProductDetail(ProductDetailObject $detail): Response
            {
                return new Response('<html>detail</html>');
            }
        };
    }

    private function createDetailHandlerMock(): ProductDetailQueryHandlerContract&MockObject
    {
        $variant = $this->createMock(ProductVariant::class);

        $stock = $this->createMock(ProductVariantStock::class);

        $price = new ProductPriceObject(99.99, 89.99, true);
        $detail = new ProductDetailObject($variant, [], $stock, $price, [], false);

        $handler = $this->createMock(ProductDetailQueryHandlerContract::class);
        $handler->method('handle')->willReturn($detail);

        return $handler;
    }
}
