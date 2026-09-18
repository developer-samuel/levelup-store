<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Segment\Order\Repository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Enum\OrderStatus
};

use App\Core\Domain\Segment\User\Entity\User;

use App\Infrastructure\Segment\Order\Repository\OrderRepository;

use Tests\{
    Support\Factory\OrderFactory,
    Support\Factory\UserFactory,
    Support\Provides\DateRange,
    Support\Provides\Persistence
};

/**
 * @coversDefaultClass \App\Infrastructure\Segment\Order\Repository\OrderRepository
*/
final class OrderRepositoryTest extends KernelTestCase
{
    use Persistence;
    use DateRange;
    use UserFactory;
    use OrderFactory;

    private EntityManagerInterface $em;
    private OrderRepository $repository;
    private User $user;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = $this->getEntityManager();
        $this->repository = $this->getRepository();

        $this->em->beginTransaction();

        $this->user = $this->createAndPersistUser('test@example.com');
    }

    protected function tearDown(): void
    {
        $this->em->rollback();

        parent::tearDown();
    }

    public function testGetOrderReturnsOrderWhenFound(): void
    {
        $order = $this->createAndPersistOrder($this->user, 'ORDER-GET-001');

        $result = $this->repository->getOrder($order->getId());

        self::assertInstanceOf(Order::class, $result);
        self::assertSame($order->getId(), $result->getId());
    }

    public function testGetOrderReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->getOrder(999999);

        self::assertNull($result);
    }

    public function testGetOrderByCodeReturnsOrderWhenFound(): void
    {
        $this->createAndPersistOrder($this->user, 'ORDER-CODE-001');

        $result = $this->repository->getOrderByCode('ORDER-CODE-001');

        self::assertInstanceOf(Order::class, $result);
        self::assertSame('ORDER-CODE-001', $result->getCode());
    }

    public function testGetOrderByCodeReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->getOrderByCode('NONEXISTENT-CODE');

        self::assertNull($result);
    }

    public function testFindOneByCodeReturnsCaseInsensitive(): void
    {
        $this->createAndPersistOrder($this->user, 'ORDER-CASE-001');

        $result = $this->repository->findOne(['code' => 'order-case-001']);

        self::assertInstanceOf(Order::class, $result);
    }

    public function testFindOneReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findOne(['code' => 'NO-SUCH-CODE']);

        self::assertNull($result);
    }

    public function testFindAllForUserReturnsOnlyUserOrders(): void
    {
        $userA = $this->createAndPersistUser('1-test@example.com');
        $userB = $this->createAndPersistUser('2-test@example.com');

        $this->createAndPersistOrder($userA, 'ORDER-USERA-001');
        $this->createAndPersistOrder($userA, 'ORDER-USERA-002');
        $this->createAndPersistOrder($userB, 'ORDER-USERB-001');

        $result = $this->repository->findAllForUser($userA);

        self::assertCount(2, $result);
        foreach ($result as $order) {
            self::assertSame($userA->getId(), $order->getUser()->getId());
        }
    }

    public function testFindAllForUserReturnsEmptyWhenNoOrders(): void
    {
        $result = $this->repository->findAllForUser($this->user);

        self::assertEmpty($result);
    }

    public function testFindOrdersByStatusesReturnsMatchingOrders(): void
    {
        $this->createAndPersistOrder($this->user, 'ORDER-PROC-001', OrderStatus::PROCESSED);
        $this->createAndPersistOrder($this->user, 'ORDER-COMP-001', OrderStatus::COMPLETED);
        $this->createAndPersistOrder($this->user, 'ORDER-REFUND-001', OrderStatus::REFUNDED);

        $result = $this->repository->findOrdersByStatuses([
            OrderStatus::PROCESSED,
            OrderStatus::COMPLETED,
        ]);

        $statuses = array_map(fn(Order $o) => $o->getStatus(), $result);

        self::assertContains(OrderStatus::PROCESSED, $statuses);
        self::assertContains(OrderStatus::COMPLETED, $statuses);
        self::assertNotContains(OrderStatus::REFUNDED, $statuses);
    }

    public function testCountOrdersBetweenCountsWithinRange(): void
    {
        [$from, $to] = $this->dateRangeNow();

        $this->createAndPersistOrder($this->user, 'ORDER-CNT-001');
        $this->createAndPersistOrder($this->user, 'ORDER-CNT-002');

        $count = $this->repository->countOrdersBetween($from, $to);

        self::assertGreaterThanOrEqual(2, $count);
    }

    public function testCountOrdersBetweenReturnsZeroForFutureRange(): void
    {
        [$from, $to] = $this->dateRangeFuture();

        $count = $this->repository->countOrdersBetween($from, $to);

        self::assertSame(0, $count);
    }

    public function testFindAllReturnsArrayOfOrders(): void
    {
        $this->createAndPersistOrder($this->user, 'ORDER-ALL-001');

        $result = $this->repository->findAll();

        self::assertNotEmpty($result);
        self::assertContainsOnlyInstancesOf(Order::class, $result);
    }

    public function testFindOneByCodeAndUserReturnsOrder(): void
    {
        $order = $this->createAndPersistOrder($this->user, 'ORDER-USER-001');

        $result = $this->repository->findOne([
            'code' => 'ORDER-USER-001',
            'user' => $this->user,
        ]);

        self::assertInstanceOf(Order::class, $result);
        self::assertSame($order->getId(), $result->getId());
    }

    public function testFindOneByCodeAndUserReturnsNullForWrongUser(): void
    {
        $userA = $this->createAndPersistUser('1-test@example.com');
        $userB = $this->createAndPersistUser('2-test@example.com');

        $this->createAndPersistOrder($userA, 'ORDER-WRONG-USER-001');

        $result = $this->repository->findOne([
            'code' => 'ORDER-WRONG-USER-001',
            'user' => $userB,
        ]);

        self::assertNull($result);
    }

    public function testCountPaidOrdersBetweenReturnsInt(): void
    {
        [$from, $to] = $this->dateRangeNow();

        $count = $this->repository->countPaidOrdersBetween($from, $to);

        self::assertGreaterThanOrEqual(0, $count);
    }

    public function testCountUnpaidOrdersBetweenReturnsInt(): void
    {
        [$from, $to] = $this->dateRangeNow();

        $this->createAndPersistOrder($this->user, 'ORDER-UNPAID-001');

        $count = $this->repository->countUnpaidOrdersBetween($from, $to);

        self::assertGreaterThanOrEqual(0, $count);
    }

    private function getRepository(): OrderRepository
    {
        $repository = static::getContainer()->get(OrderRepository::class);
        assert($repository instanceof OrderRepository);

        return $repository;
    }
}
