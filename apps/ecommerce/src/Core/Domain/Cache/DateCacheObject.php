<?php

declare(strict_types=1);

namespace App\Core\Domain\Cache;

/**
 * @phpstan-type ObjectArray array{
 *     currentDate: string,
 *     currentDay: string,
 *     currentWeek: string,
 *     currentMonth: string,
 *     currentYear: string
 * }
*/
final readonly class DateCacheObject
{
    /**
     * @param string $currentDate
     * @param string $currentDay
     * @param string $currentWeek
     * @param string $currentMonth
     * @param string $currentYear
    */
    public function __construct(
        public string $currentDate,
        public string $currentDay,
        public string $currentWeek,
        public string $currentMonth,
        public string $currentYear,
    ) {}

    /**
     * @param \DateTime $date
     *
     * @return self
    */
    public static function fromDate(\DateTime $date): self
    {
        return new self(
            currentDate: $date->format('Y-m-d'),
            currentDay: $date->format('D'),
            currentWeek: $date->format('W'),
            currentMonth: $date->format('F'),
            currentYear: $date->format('Y'),
        );
    }

    /**
     * @return ObjectArray
    */
    public function toArray(): array
    {
        return [
            'currentDate'  => $this->currentDate,
            'currentDay'   => $this->currentDay,
            'currentWeek'  => $this->currentWeek,
            'currentMonth' => $this->currentMonth,
            'currentYear'  => $this->currentYear,
        ];
    }
}
