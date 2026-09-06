<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Segment\User\Repository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Segment\User\Repository\UserRepositoryContract;

use App\Infrastructure\Segment\User\Repository\UserRepository;

use Tests\{
    Support\Factory\UserFactory,
    Support\Provides\DateRange,
    Support\Provides\Persistence
};

/**
 * @coversDefaultClass \App\Infrastructure\Segment\User\Repository\UserRepository
*/
final class UserRepositoryTest extends KernelTestCase
{
    use Persistence;
    use DateRange;
    use UserFactory;

    private EntityManagerInterface $em;
    private UserRepository $repository;
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

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(UserRepositoryContract::class, $this->repository);
    }

    public function testFindByEmailReturnsUserWhenFound(): void
    {
        $result = $this->repository->findByEmail($this->user->getEmail());

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($this->user->getEmail(), $result->getEmail());
    }

    public function testFindByEmailReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findByEmail('nonexistent@example.com');

        $this->assertNull($result);
    }

    public function testFindByEmailIsCaseInsensitive(): void
    {
        $result = $this->repository->findByEmail(strtoupper($this->user->getEmail()));

        $this->assertInstanceOf(User::class, $result);
    }

    public function testFindByIdReturnsUserWhenFound(): void
    {
        $result = $this->repository->findById($this->user->getId());

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($this->user->getId(), $result->getId());
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findById(999999);

        $this->assertNull($result);
    }

    public function testCountUsersBetweenReturnsCorrectCount(): void
    {
        [$from, $to] = $this->dateRangeNow();

        $this->createAndPersistUser('1-test@example.com');
        $this->createAndPersistUser('2-test@example.com');

        $count = $this->repository->countUsersBetween($from, $to);

        $this->assertGreaterThanOrEqual(2, $count);
    }

    public function testFindAllReturnsArray(): void
    {
        $result = $this->repository->findAll();

        $this->assertNotEmpty($result);
        $this->assertContainsOnlyInstancesOf(User::class, $result);
    }

    public function testCountUsersBetweenReturnsZeroForFutureRange(): void
    {
        [$from, $to] = $this->dateRangeFuture();

        $count = $this->repository->countUsersBetween($from, $to);

        $this->assertSame(0, $count);
    }

    private function getRepository(): UserRepository
    {
        $repository = static::getContainer()->get(UserRepository::class);
        assert($repository instanceof UserRepository);

        return $repository;
    }
}
