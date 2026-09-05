<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Auth\Repository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\Core\Domain\{
    Auth\Entity\RefreshToken,
    Segment\User\Entity\User
};

use App\Core\Ports\Auth\Repository\RefreshTokenRepositoryContract;

use App\Infrastructure\Auth\Repository\RefreshTokenRepository;

use Tests\{
    Support\Factory\UserFactory,
    Support\Provides\Persistence
};

/**
 * @coversDefaultClass \App\Infrastructure\Auth\Repository\RefreshTokenRepository
*/
final class RefreshTokenRepositoryTest extends KernelTestCase
{
    use Persistence;
    use UserFactory;

    private EntityManagerInterface $em;
    private RefreshTokenRepository $repository;
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
        $this->assertInstanceOf(RefreshTokenRepositoryContract::class, $this->repository);
    }

    public function testCreatePersistsRefreshToken(): void
    {
        $token = $this->repository->create($this->user);

        $this->assertInstanceOf(RefreshToken::class, $token);
        $this->assertNotEmpty($token->getToken());
        $this->assertFalse($token->isExpired());
    }

    public function testCreateGeneratesUniqueTokens(): void
    {
        $tokenA = $this->repository->create($this->user);
        $tokenB = $this->repository->create($this->user);

        $this->assertNotSame($tokenA->getToken(), $tokenB->getToken());
    }

    public function testFindByTokenReturnsTokenWhenFound(): void
    {
        $token = $this->repository->create($this->user);

        $found = $this->repository->findByToken($token->getToken());

        $this->assertInstanceOf(RefreshToken::class, $found);
        $this->assertSame($token->getToken(), $found->getToken());
    }

    public function testFindByTokenReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findByToken('non-existing-token-string');

        $this->assertNull($result);
    }

    public function testRevokeRemovesToken(): void
    {
        $token = $this->repository->create($this->user);
        $tokenValue = $token->getToken();

        $this->repository->revoke($token);

        $found = $this->repository->findByToken($tokenValue);

        $this->assertNull($found);
    }

    public function testCreatedTokenIsNotExpired(): void
    {
        $token = $this->repository->create($this->user);

        $this->assertFalse($token->isExpired());
    }

    private function getRepository(): RefreshTokenRepository
    {
        $repository = static::getContainer()->get(RefreshTokenRepository::class);
        assert($repository instanceof RefreshTokenRepository);

        return $repository;
    }
}
