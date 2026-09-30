<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Service\Query;

use App\Core\Domain\{
    Segment\User\Entity\UserBilling,
    Segment\User\Entity\UserShipping
};

/**
 * @phpstan-type AddressData array{
 *     countryId: int,
 *     street: string,
 *     postalCode: string,
 *     city: string
 * }
*/
interface UserAddressQueryContract
{
    /**
     * @param array<string, mixed> $data
     *
     * @return AddressData
    */
    public function extractAndSanitizeAddressData(array $data): array;

    /**
     * @param UserBilling|UserShipping $entity
     *
     * @return bool
    */
    public function shouldRemoveEntity(UserBilling|UserShipping $entity): bool;
}
