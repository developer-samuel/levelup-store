<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Segment\Cart\Repository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\Core\Domain\Segment\Cart\Entity\Cart;

use App\Core\Ports\Segment\Cart\Repository\CartRepositoryContract;

use App\Infrastructure\Segment\Cart\Repository\CartRepository;

use Tests\{
    Support\Factory\CartFactory,
    Support\Factory\ProductVariantFactory,
    Support\Factory\UserFactory,
    Support\Provides\Persistence
};

/**
 * @coversDefaultClass \App\Infrastructure\Segment\Cart\Repository\CartRepository
*/
final class CartRepositoryTest extends KernelTestCase
{
    use Persistence;
    use UserFactory;
    use CartFactory;
    use ProductVariantFactory;

    private EntityManagerInterface $em;
    private CartRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = $this->getEntityManager();
        $this->repository = $this->getRepository();

        $this->em->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->rollback();

        parent::tearDown();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(CartRepositoryContract::class, $this->repository);
    }

    public function testFindCartForUserReturnsCartWhenExists(): void
    {
        $user = $this->createAndPersistUser('test@example.com');
        $this->createAndPersistCart($user);

        $result = $this->repository->findCartForUser($user->getId());

        $this->assertInstanceOf(Cart::class, $result);
        $this->assertSame($user->getId(), $result->getUser()->getId());
    }

    public function testFindCartForUserReturnsNullWhenNotExists(): void
    {
        $result = $this->repository->findCartForUser(999999);

        $this->assertNull($result);
    }

    public function testFindCartForUserReturnsOnlyCartForGivenUser(): void
    {
        $userA = $this->createAndPersistUser('1-test@example.com');
        $userB = $this->createAndPersistUser('2-test@example.com');

        $this->createAndPersistCart($userA);
        $this->createAndPersistCart($userB);

        $result = $this->repository->findCartForUser($userA->getId());

        $this->assertNotNull($result);
        $this->assertSame($userA->getId(), $result->getUser()->getId());
    }

    public function testFindInactiveSinceReturnsCartUpdatedBeforeThreshold(): void
    {
        $user = $this->createAndPersistUser('inactive@example.com');
        $cart = $this->createAndPersistCart($user);

        $this->forceUpdatedAt($cart, new \DateTimeImmutable('-2 days'));

        $result = $this->repository->findInactiveSince(new \DateTimeImmutable('-1 day'));

        $this->assertContains($cart->getId(), $this->extractIds($result));
    }

    public function testFindInactiveSinceDoesNotReturnRecentCart(): void
    {
        $user = $this->createAndPersistUser('recent@example.com');
        $cart = $this->createAndPersistCart($user);

        $this->forceUpdatedAt($cart, new \DateTimeImmutable('+1 hour'));

        $result = $this->repository->findInactiveSince(new \DateTimeImmutable('now'));

        $this->assertNotContains($cart->getId(), $this->extractIds($result));
    }

    public function testFindInactiveSinceReturnsEmptyWhenNoCartsMatchThreshold(): void
    {
        $result = $this->repository->findInactiveSince(new \DateTimeImmutable('-10 years'));

        $this->assertEmpty($result);
    }

    public function testFindAbandonedForReminderReturnsCartInWindow(): void
    {
        $user = $this->createAndPersistUser('abandoned@example.com');
        $cart = $this->createAndPersistCart($user);
        $variant = $this->createAndPersistVariant('SKU-AB-001', 'Variant Abandoned', 'variant-ab-001');
        $this->createAndPersistCartItem($cart, $variant);

        $this->forceUpdatedAt($cart, new \DateTimeImmutable('-2 days'));

        $from = new \DateTimeImmutable('-1 day');
        $to = new \DateTimeImmutable('-7 days');

        $result = $this->repository->findAbandonedForReminder($from, $to);

        $this->assertContains($cart->getId(), $this->extractIds($result));
    }

    public function testFindAbandonedForReminderExcludesAlreadyReminded(): void
    {
        $user = $this->createAndPersistUser('reminded@example.com');
        $cart = $this->createAndPersistCart($user);
        $variant = $this->createAndPersistVariant('SKU-AB-002', 'Variant Reminded', 'variant-ab-002');
        $this->createAndPersistCartItem($cart, $variant);

        $this->forceUpdatedAt($cart, new \DateTimeImmutable('-2 days'));
        $this->forceReminderSentAt($cart, new \DateTimeImmutable('-1 day'));

        $from = new \DateTimeImmutable('-1 day');
        $to = new \DateTimeImmutable('-7 days');

        $result = $this->repository->findAbandonedForReminder($from, $to);

        $this->assertNotContains($cart->getId(), $this->extractIds($result));
    }

    public function testFindAbandonedForReminderExcludesEmptyCarts(): void
    {
        $user = $this->createAndPersistUser('emptyabandoned@example.com');
        $cart = $this->createAndPersistCart($user);

        $this->forceUpdatedAt($cart, new \DateTimeImmutable('-2 days'));

        $from = new \DateTimeImmutable('-1 day');
        $to = new \DateTimeImmutable('-7 days');

        $result = $this->repository->findAbandonedForReminder($from, $to);

        $this->assertNotContains($cart->getId(), $this->extractIds($result));
    }

    public function testFindAbandonedForReminderExcludesRecentCarts(): void
    {
        $user = $this->createAndPersistUser('recent2@example.com');
        $cart = $this->createAndPersistCart($user);
        $variant = $this->createAndPersistVariant('SKU-AB-003', 'Variant Recent', 'variant-ab-003');
        $this->createAndPersistCartItem($cart, $variant);

        $this->forceUpdatedAt($cart, new \DateTimeImmutable('-30 minutes'));

        $from = new \DateTimeImmutable('-1 day');
        $to = new \DateTimeImmutable('-7 days');

        $result = $this->repository->findAbandonedForReminder($from, $to);

        $this->assertNotContains($cart->getId(), $this->extractIds($result));
    }

    public function testFindEmptyReturnsCartWithNoItems(): void
    {
        $user = $this->createAndPersistUser('empty@example.com');
        $cart = $this->createAndPersistCart($user);

        $result = $this->repository->findEmpty();

        $this->assertContains($cart->getId(), $this->extractIds($result));
    }

    public function testFindEmptyDoesNotReturnCartWithItems(): void
    {
        $user = $this->createAndPersistUser('notempty@example.com');
        $cart = $this->createAndPersistCart($user);
        $variant = $this->createAndPersistVariant('SKU-NE-001', 'Variant Not Empty', 'variant-ne-001');

        $this->createAndPersistCartItem($cart, $variant);

        $result = $this->repository->findEmpty();

        $this->assertNotContains($cart->getId(), $this->extractIds($result));
    }

    public function testFindEmptyReturnsOnlyEmptyCarts(): void
    {
        $userA = $this->createAndPersistUser('empty-a@example.com');
        $userB = $this->createAndPersistUser('empty-b@example.com');

        $emptyCart = $this->createAndPersistCart($userA);
        $fullCart = $this->createAndPersistCart($userB);
        $variant = $this->createAndPersistVariant('SKU-OE-001', 'Variant Only Empty', 'variant-oe-001');

        $this->createAndPersistCartItem($fullCart, $variant);

        $result = $this->repository->findEmpty();
        $ids = $this->extractIds($result);

        $this->assertContains($emptyCart->getId(), $ids);
        $this->assertNotContains($fullCart->getId(), $ids);
    }

    /**
     * @param Cart[] $carts
     *
     * @return int[]
    */
    private function extractIds(array $carts): array
    {
        return array_map(fn(Cart $c) => $c->getId(), $carts);
    }

    private function forceUpdatedAt(Cart $cart, \DateTimeImmutable $date): void
    {
        $this->em->createQuery(
            'UPDATE ' . Cart::class . ' c SET c.updatedAt = :date WHERE c.id = :id',
        )
        ->setParameter('date', $date)
        ->setParameter('id', $cart->getId())
        ->execute();
    }

    private function forceReminderSentAt(Cart $cart, \DateTimeImmutable $date): void
    {
        $this->em->createQuery(
            'UPDATE ' . Cart::class . ' c SET c.reminderSentAt = :date WHERE c.id = :id',
        )
        ->setParameter('date', $date)
        ->setParameter('id', $cart->getId())
        ->execute();
    }

    private function getRepository(): CartRepository
    {
        $repository = static::getContainer()->get(CartRepository::class);
        assert($repository instanceof CartRepository);

        return $repository;
    }
}
