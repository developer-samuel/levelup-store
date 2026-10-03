<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Country;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\Segment\Country\Entity\Country;

use App\Core\Ports\Segment\Country\CountryRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection
};

/** @extends AbstractRepository<Country> */
final class CountryRepository extends AbstractRepository implements CountryRepositoryContract
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Country::class,
        );
    }

    protected function getAlias(): string
    {
        return 'c';
    }

    protected function getFindAllSortColumn(): string
    {
        return 'id';
    }

    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }

    /** @return Country[] */
    public function findAllByCode(string $code): array
    {
        return $this->findBy(['code' => $code]);
    }

    public function findById(int $id): ?Country
    {
        return $this->find($id);
    }
}
