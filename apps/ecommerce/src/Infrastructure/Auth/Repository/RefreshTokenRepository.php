<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Repository;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\{
    Auth\Entity\RefreshToken,
    Segment\User\Entity\User
};

use App\Core\Ports\Auth\Repository\RefreshTokenRepositoryContract;

use App\Infrastructure\Abstract\Repository\AbstractTokenRepository;

/** @extends AbstractTokenRepository<RefreshToken> */
final class RefreshTokenRepository extends AbstractTokenRepository implements RefreshTokenRepositoryContract
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly int $refreshTokenTtl,
    ) {
        parent::__construct($registry, RefreshToken::class);
    }

    public function create(User $user): RefreshToken
    {
        $token = (new RefreshToken())
            ->setToken(bin2hex(random_bytes(64)))
            ->setUser($user)
            ->setExpiresAt(new \DateTimeImmutable(sprintf('+%d seconds', $this->refreshTokenTtl)));

        $em = $this->getEntityManager();
        $em->persist($token);
        $em->flush();

        return $token;
    }

    public function findByToken(string $token): ?RefreshToken
    {
        /** @var RefreshToken|null $result */
        $result = parent::findByToken($token);

        return $result;
    }

    public function revoke(RefreshToken $token): void
    {
        $em = $this->getEntityManager();
        $em->remove($token);
        $em->flush();
    }

    protected function getAlias(): string
    {
        return 'rt';
    }
}
