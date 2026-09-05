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

/**
 * @extends AbstractRepository<Country>
*/
class CountryRepository extends AbstractRepository implements CountryRepositoryContract
{
    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Country::class,
        );
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 'c';
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
     * @param string $code
     *
     * @return Country[]
    */
    public function findAllByCode(string $code): array
    {
        return $this->findBy(['code' => $code]);
    }

    /**
     * @param int $id
     *
     * @return Country|null
    */
    public function findById(int $id): ?Country
    {
        return $this->find($id);
    }
}
