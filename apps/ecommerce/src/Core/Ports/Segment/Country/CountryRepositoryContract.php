<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Country;

use App\Core\Domain\Segment\Country\Entity\Country;

interface CountryRepositoryContract
{
    /** @return Country[] */
    public function findAll(): array;

    /** @return Country[] */
    public function findAllByCode(string $code): array;

    public function findById(int $id): ?Country;
}
