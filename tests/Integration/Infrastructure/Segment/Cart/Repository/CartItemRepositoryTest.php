<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Segment\Cart\Repository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Cart\Entity\CartItem,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User
};

use App\Core\Ports\Segment\Cart\Repository\CartItemRepositoryContract;

use App\Infrastructure\Segment\Cart\Repository\CartItemRepository;

use Tests\{
    Support\Factory\CartFactory,
    Support\Factory\ProductVariantFactory,
    Support\Factory\UserFactory,
    Support\Provides\Persistence
};

/**
 * @coversDefaultClass \App\Infrastructure\Segment\Cart\Repository\CartItemRepository
*/
final class CartItemRepositoryTest extends KernelTestCase
{
    use Persistence;
    use UserFactory;
    use CartFactory;
    use ProductVariantFactory;

    private EntityManagerInterface $em;
    private CartItemRepository $repository;
    private User $user;
    private Cart $cart;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = $this->getEntityManager();
        $this->repository = $this->getRepository();

        $this->em->beginTransaction();

        $this->user = $this->createAndPersistUser('test@example.com');
        $this->cart = $this->createAndPersistCart($this->user);
    }

    protected function tearDown(): void
    {
        $this->em->rollback();

        parent::tearDown();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(CartItemRepositoryContract::class, $this->repository);
    }

    public function testGetItemReturnsCartItemWhenExists(): void
    {
        $variant = $this->createAndPersistVariant('SKU-GET-001', 'Variant Get', 'variant-get');
        $item = $this->createAndPersistCartItem($this->cart, $variant);

        $itemId = $item->getId();
        assert($itemId !== null);

        $result = $this->repository->getItem($itemId);

        $this->assertInstanceOf(CartItem::class, $result);
        $this->assertSame($itemId, $result->getId());
    }

    public function testGetItemReturnsNullWhenNotExists(): void
    {
        $result = $this->repository->getItem(999999);

        $this->assertNull($result);
    }

    public function testFindByCartReturnsItemsForCart(): void
    {
        $variant = $this->createAndPersistVariant('SKU-FBC-001', 'Variant FBC', 'variant-fbc');

        $this->createAndPersistCartItem($this->cart, $variant);

        $result = $this->repository->findByCart($this->cart);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(CartItem::class, $result[0]);
    }

    public function testFindByCartReturnsEmptyWhenNoItems(): void
    {
        $result = $this->repository->findByCart($this->cart);

        $this->assertEmpty($result);
    }

    public function testFindAllWithVariantReturnsCartItems(): void
    {
        $variant = $this->createAndPersistVariant('SKU-FAV-001', 'Variant FAV', 'variant-fav');
        $this->createAndPersistCartItem($this->cart, $variant);

        $result = $this->repository->findAllWithVariant();

        $this->assertNotEmpty($result);
        $this->assertContainsOnlyInstancesOf(CartItem::class, $result);
    }

    public function testFindAllWithVariantReturnsEmptyWhenNoItems(): void
    {
        $result = $this->repository->findAllWithVariant();

        $this->assertEmpty($result);
    }

    public function testFindAllWithVariantEagerLoadsVariantAndStock(): void
    {
        $variant = $this->createAndPersistVariant('SKU-FAV-002', 'Variant FAV Stock', 'variant-fav-stock');
        $this->createAndPersistCartItem($this->cart, $variant);

        $this->em->clear();

        $result = $this->repository->findAllWithVariant();

        $this->assertNotEmpty($result);

        $loadedVariant = $result[0]->getVariant();

        $this->assertInstanceOf(ProductVariant::class, $loadedVariant);
    }

    public function testFindByCartReturnsOnlyItemsForGivenCart(): void
    {
        $userB = $this->createAndPersistUser('2-test@example.com');
        $cartB = $this->createAndPersistCart($userB);
        $variantA = $this->createAndPersistVariant('SKU-MC-A', 'Variant MC A', 'variant-mc-a');
        $variantB = $this->createAndPersistVariant('SKU-MC-B', 'Variant MC B', 'variant-mc-b');

        $this->createAndPersistCartItem($this->cart, $variantA);
        $this->createAndPersistCartItem($cartB, $variantB);

        $result = $this->repository->findByCart($this->cart);

        $this->assertCount(1, $result);

        $cart = $result[0]->getCart();
        assert($cart !== null);
        
        $this->assertSame($this->cart->getId(), $cart->getId());
    }

    private function getRepository(): CartItemRepository
    {
        $repository = static::getContainer()->get(CartItemRepository::class);
        assert($repository instanceof CartItemRepository);

        return $repository;
    }
}
