<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Country;

use App\Core\Domain\Segment\Country\Entity\Country;

interface CountryRepositoryContract
{
    /**
     * @return Country[]
    */
    public function findAll(): array;

    /**
     * @param string $code
     *
     * @return Country[]
    */
    public function findAllByCode(string $code): array;

    /**
     * @param int $id
     *
     * @return Country|null
    */
    public function findById(int $id): ?Country;
}
