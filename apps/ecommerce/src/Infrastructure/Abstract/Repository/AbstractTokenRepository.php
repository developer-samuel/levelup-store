<?php

declare(strict_types=1);

namespace App\Infrastructure\Abstract\Repository;

use Doctrine\{
    Bundle\DoctrineBundle\Repository\ServiceEntityRepository,
    Persistence\ManagerRegistry
};

use App\Core\Domain\Segment\User\Entity\User;

use App\Infrastructure\Shared\Traits\SingleResult;

/**
 * @template TEntity of object
 *
 * @extends ServiceEntityRepository<TEntity>
*/
abstract class AbstractTokenRepository extends ServiceEntityRepository
{
    use SingleResult;

    /** @param class-string<TEntity> $entityClass */
    public function __construct(
        ManagerRegistry $registry,
        string $entityClass,
    ) {
        parent::__construct(
            $registry,
            $entityClass,
        );
    }

    abstract protected function getAlias(): string;

    final public function removeTokensByUser(User $user): void
    {
        $this->getEntityManager()->createQueryBuilder()
            ->delete($this->getEntityName(), $this->getAlias())
            ->where(sprintf('%s.user = :user', $this->getAlias()))
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    final public function deleteExpired(): int
    {
        /** @var int $deleted */
        $deleted = $this->getEntityManager()->createQueryBuilder()
            ->delete($this->getEntityName(), $this->getAlias())
            ->where(sprintf('%s.expiresAt < :now', $this->getAlias()))
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();

        return $deleted;
    }

    /** @return TEntity|null */
    public function findByToken(string $token): ?object
    {
        $qb = $this->createQueryBuilder($this->getAlias())
            ->where(sprintf('%s.token = :token', $this->getAlias()))
            ->setParameter('token', $token);

        /** @var TEntity|null $result */
        $result = $this->getResultOrNull($qb);

        return $result;
    }
}
