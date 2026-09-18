<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Enum;

enum SortDirection: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';

    /**
     * @return \SortDirection
    */
    public function sort(): \SortDirection
    {
        return match ($this) {
            self::ASC  => \SortDirection::Ascending,
            self::DESC => \SortDirection::Descending,
        };
    }
}
