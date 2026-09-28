<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Service\Query;

use Kit\Utils\Shared\DataSanitizer;

use App\Core\Domain\{
    Segment\User\Entity\UserBilling,
    Segment\User\Entity\UserShipping
};

use App\Core\Ports\Segment\User\Service\Query\UserAddressQueryContract;

/**
 * @phpstan-import-type AddressData from UserAddressQueryContract
*/
final class UserAddressQueryService implements UserAddressQueryContract
{
    /**
     * @param array<string, mixed> $data
     *
     * @return AddressData
    */
    public function extractAndSanitizeAddressData(array $data): array
    {
        $countryId = DataSanitizer::sanitizeInt($data['country'] ?? null) ?? 0;
        $street = DataSanitizer::sanitizeString($data['street'] ?? null);
        $postalCode = DataSanitizer::sanitizeString($data['postalCode'] ?? null);
        $city = DataSanitizer::sanitizeString($data['city'] ?? null);

        return [
            'countryId'  => $countryId,
            'street'     => $street,
            'postalCode' => $postalCode,
            'city'       => $city,
        ];
    }

    /**
     * @param UserBilling|UserShipping $entity
     *
     * @return bool
    */
    public function shouldRemoveEntity(UserBilling|UserShipping $entity): bool
    {
        $streetEmpty = $entity->getStreet() === '';
        $postalEmpty = $entity->getPostalCode() === '';
        $countryEmpty = $entity->getCountry() === null;
        $cityEmpty = $entity->getCity() === '';

        return $streetEmpty && $postalEmpty && $countryEmpty && $cityEmpty;
    }
}
