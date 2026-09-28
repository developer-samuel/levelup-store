<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\User\Repository;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Segment\User\Repository\UserRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection,
    Shared\Traits\DateRange,
    Shared\Traits\SingleResult
};

/**
 * @extends AbstractRepository<User>
*/
final class UserRepository extends AbstractRepository implements UserRepositoryContract
{
    use DateRange;
    use SingleResult;

    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            User::class,
        );
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 'u';
    }

    /**
     * @return string
    */
    protected function getFindAllSortColumn(): string
    {
        return 'id';
    }

    /**
     * @return SortDirection
    */
    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }

    /**
     * @param int $id
     *
     * @return User|null
    */
    public function findById(int $id): ?User
    {
        return $this->find($id);
    }

    /**
     * @param string $email
     *
     * @return User|null
    */
    public function findByEmail(string $email): ?User
    {
        return $this->findOneByColumn('email', $email);
    }

    /**
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     *
     * @return int
    */
    public function countUsersBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int
    {
        $qb = $this->applyDateRange(
            $this->createQueryBuilder('u'),
            'u',
            $from,
            $to,
        )->select('COUNT(u.id)');

        return $this->getScalarIntResult($qb);
    }
}
