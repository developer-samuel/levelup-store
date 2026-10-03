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

/** @extends AbstractRepository<User> */
final class UserRepository extends AbstractRepository implements UserRepositoryContract
{
    use DateRange;
    use SingleResult;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            User::class,
        );
    }

    protected function getAlias(): string
    {
        return 'u';
    }

    protected function getFindAllSortColumn(): string
    {
        return 'id';
    }

    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }

    public function findById(int $id): ?User
    {
        return $this->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneByColumn('email', $email);
    }

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
