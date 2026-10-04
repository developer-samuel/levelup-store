<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Order\Resource;

use Packages\Kit\Utils\Shared\StringNormalizer;

use App\Core\Domain\Segment\Order\Entity\Order;

use App\Shared\Utils\Formatter\DateTimeFormatter;

/**
 * @phpstan-type ResourceArray array{
 *     id: int,
 *     code: string,
 *     price: string,
 *     payment: string,
 *     isPaid: bool,
 *     status: string,
 *     createdAt: string
 * }
*/
final class AdminApiOrderResource
{
    /** @return ResourceArray */
    public static function toArray(Order $order): array
    {
        return [
            'id'        => $order->getId(),
            'code'      => $order->getCode(),
            'price'     => $order->getPrice() . ' €',
            'payment'   => StringNormalizer::capitalizeAndReplaceUnderscoresWithSpaces($order->getPayment()->value),
            'isPaid'    => $order->getOrderPayment() !== null,
            'status'    => StringNormalizer::capitalizeAndReplaceUnderscoresWithSpaces($order->getStatus()->value),
            'createdAt' => DateTimeFormatter::formatDMY($order->getCreatedAt()),
        ];
    }
}
