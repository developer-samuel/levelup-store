<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\Country;

use App\Core\Domain\Segment\Country\Entity\Country;

final readonly class CountryAssertion
{
    /**
     * @param array<mixed> $response
     *
     * @return list<array{
     *     alpha2Code: string,
     *     name: string
     * }>
    */
    public static function assertResponseFormat(array $response): array
    {
        if (!array_is_list($response)) {
            throw new \LogicException('Response must be a list.');
        }

        return self::validateResponseItems($response);
    }

    /**
     * @param Country|null $country
     * @param int|null $countryId
     *
     * @return void
     *
     * @throws \InvalidArgumentException
    */
    public static function assertExistsForId(?Country $country, ?int $countryId): void
    {
        if ($country === null) {
            throw new \InvalidArgumentException('Country not found for ID: ' . $countryId);
        }
    }

    /**
     * @param list<mixed> $items
     *
     * @return list<array{
     *     alpha2Code: string,
     *     name: string
     * }>
    */
    private static function validateResponseItems(array $items): array
    {
        $result = [];

        foreach ($items as $index => $item) {
            $result[] = self::validateResponseItem($item, $index);
        }

        return $result;
    }

    /**
     * @param mixed $item
     *
     * @return array{
     *     alpha2Code: string,
     *     name: string
     * }
    */
    private static function validateResponseItem(mixed $item, int $index): array
    {
        if (
            !is_array($item) ||
            !isset($item['alpha2Code'], $item['name']) ||
            !is_string($item['alpha2Code']) ||
            !is_string($item['name'])
        ) {
            throw new \LogicException(
                sprintf('Invalid country structure at index %d.', $index),
            );
        }

        return [
            'alpha2Code' => $item['alpha2Code'],
            'name'       => $item['name'],
        ];
    }
}
