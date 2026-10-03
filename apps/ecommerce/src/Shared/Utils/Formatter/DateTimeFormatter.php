<?php

declare(strict_types=1);

namespace App\Shared\Utils\Formatter;

final class DateTimeFormatter
{
    public static function format(\DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s');
    }

    public static function formatShort(\DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i');
    }

    public static function formatDMY(\DateTimeInterface $dateTime): string
    {
        return $dateTime->format('d.m.Y H:i');
    }

    public static function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return self::unit($seconds, 'second');
        }

        if ($seconds < 3600) {
            return trim(self::unit(intdiv($seconds, 60), 'minute') . ' ' . self::unit($seconds % 60, 'second', skip: true));
        }

        return trim(self::unit(intdiv($seconds, 3600), 'hour') . ' ' . self::unit(intdiv($seconds % 3600, 60), 'minute', skip: true));
    }

    private static function unit(int $value, string $unit, bool $skip = false): string
    {
        if ($skip && $value === 0) {
            return '';
        }

        return sprintf('%d %s', $value, $value === 1 ? $unit : $unit . 's');
    }
}
