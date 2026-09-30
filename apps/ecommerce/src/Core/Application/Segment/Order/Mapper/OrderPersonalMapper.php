<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Mapper;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderPersonal
};

use App\Core\Application\{
    Shared\Enum\CaseType,
    Shared\Utils\Mapper\ArrayMapper
};

/**
 * @phpstan-type PersonalCamel array{
 *     firstName: string,
 *     lastName: string,
 *     email: string
 * }
 * @phpstan-type PersonalSnake array{
 *     first_name: string,
 *     last_name: string,
 *     email: string
 * }
 * @phpstan-type InternalPersonalValues array{
 *     first: string,
 *     last: string,
 *     email: string
 * }
*/
final class OrderPersonalMapper
{
    /**
     * @param Order $order
     *
     * @return PersonalCamel
    */
    public static function mapToCamelCase(Order $order): array
    {
        /** @var PersonalCamel $data */
        $data = self::mapPersonal($order, CaseType::CAMEL);

        return $data;
    }

    /**
     * @param Order $order
     *
     * @return PersonalSnake
    */
    public static function mapToSnakeCase(Order $order): array
    {
        /** @var PersonalSnake $data */
        $data = self::mapPersonal($order, CaseType::SNAKE);

        return $data;
    }

    /**
     * @param Order $order
     * @param CaseType $case
     *
     * @return PersonalCamel|PersonalSnake
    */
    private static function mapPersonal(Order $order, CaseType $case): array
    {
        $personal = $order->getPersonal();
        if (!$personal instanceof OrderPersonal) {
            /** @var PersonalCamel|PersonalSnake */
            return ArrayMapper::emptyByKeys(self::getKeysByCase($case));
        }

        $values = self::extractPersonalValues($personal);
        $keys = self::getKeysByCase($case);

        /** @var PersonalCamel|PersonalSnake */
        return ArrayMapper::mapValuesToKeys($values, $keys);
    }

    /**
     * @param OrderPersonal $personal
     *
     * @return InternalPersonalValues
    */
    private static function extractPersonalValues(OrderPersonal $personal): array
    {
        return [
            'first' => $personal->getFirstName(),
            'last'  => $personal->getLastName(),
            'email' => $personal->getEmail(),
        ];
    }

    /**
     * @param CaseType $case
     *
     * @return InternalPersonalValues
    */
    private static function getKeysByCase(CaseType $case): array
    {
        return $case === CaseType::CAMEL
            ? ['first' => 'firstName', 'last' => 'lastName', 'email' => 'email']
            : ['first' => 'first_name', 'last' => 'last_name', 'email' => 'email'];
    }
}
