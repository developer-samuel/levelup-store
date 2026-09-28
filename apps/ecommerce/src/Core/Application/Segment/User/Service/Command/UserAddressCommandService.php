<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Service\Command;

use Kit\Assertion\Domain\Country\CountryAssertion;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Entity\UserBilling,
    Segment\User\Entity\UserShipping
};

use App\Core\Ports\{
    Segment\Country\CountryRepositoryContract,
    Segment\User\Service\Command\UserAddressCommandContract,
    Segment\User\Service\Query\UserAddressQueryContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class UserAddressCommandService implements UserAddressCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param CountryRepositoryContract $countryRepository
     * @param UserAddressQueryContract $userAddressQuery
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private CountryRepositoryContract $countryRepository,
        private UserAddressQueryContract $userAddressQuery,
    ) {}

    /**
     * @param User $user
     * @param UserBilling|UserShipping|null $entity
     * @param array<string, int|string|null> $data
     * @param class-string<UserBilling|UserShipping> $entityClass
     *
     * @return void
    */
    public function processAddressEntity(
        User $user,
        UserBilling|UserShipping|null $entity,
        array $data,
        string $entityClass,
    ): void {
        $addressData = $this->userAddressQuery->extractAndSanitizeAddressData($data);

        $countryId = $addressData['countryId'];
        $street = $addressData['street'];
        $postalCode = $addressData['postalCode'];
        $city = $addressData['city'];

        $entity = $this->createEntityIfNeeded($user, $entity, $countryId, $street, $postalCode, $city, $entityClass);
        if ($entity === null) {
            return;
        }

        $this->updateOrRemoveAddressEntity($entity, $countryId, $street, $postalCode, $city);

        if ($this->userAddressQuery->shouldRemoveEntity($entity)) {
            $this->entityPersistence->remove($entity, true);
            $entity instanceof UserBilling ? $user->setBilling(null) : $user->setShipping(null);

            return;
        }

        $this->entityPersistence->persist($entity);
    }

    /**
     * @param User $user
     * @param UserBilling|UserShipping|null $entity
     * @param int $countryId
     * @param string $street
     * @param string $postalCode
     * @param string $city
     * @param class-string<UserBilling|UserShipping> $entityClass
     *
     * @return UserBilling|UserShipping|null
    */
    private function createEntityIfNeeded(
        User $user,
        UserBilling|UserShipping|null $entity,
        int $countryId,
        string $street,
        string $postalCode,
        string $city,
        string $entityClass,
    ): UserBilling|UserShipping|null {
        if ($entity === null && ($countryId > 0 || $street !== '' || $postalCode !== '' || $city !== '')) {
            return $this->createNewAddressEntity($user, $entityClass);
        }

        return $entity;
    }

    /**
     * @param User $user
     * @param class-string<UserBilling|UserShipping> $entityClass
     *
     * @return UserBilling|UserShipping
    */
    private function createNewAddressEntity(User $user, string $entityClass): UserBilling|UserShipping
    {
        $entity = new $entityClass();
        $entity->setUser($user);

        $entity instanceof UserBilling ? $user->setBilling($entity) : $user->setShipping($entity);

        return $entity;
    }

    /**
     * @param UserBilling|UserShipping $entity
     * @param int|null $countryId
     * @param string|null $street
     * @param string|null $postalCode
     * @param string|null $city
     *
     * @return void
    */
    private function updateOrRemoveAddressEntity(
        UserBilling|UserShipping $entity,
        ?int $countryId,
        ?string $street,
        ?string $postalCode,
        ?string $city,
    ): void {
        $this->updateCountry($entity, $countryId);
        $this->updateAddressFields($entity, $street, $postalCode, $city);
    }

    /**
     * @param UserBilling|UserShipping $entity
     * @param int|null $countryId
     *
     * @return void
    */
    private function updateCountry(UserBilling|UserShipping $entity, ?int $countryId): void
    {
        if ($countryId === null || $countryId === 0) {
            $entity->setCountry(null);
            return;
        }

        $country = $this->countryRepository->findById($countryId);
        CountryAssertion::assertExistsForId($country, $countryId);

        $entity->setCountry($country);
    }

    /**
     * @param UserBilling|UserShipping $entity
     * @param string|null $street
     * @param string|null $postalCode
     * @param string|null $city
     *
     * @return void
    */
    private function updateAddressFields(
        UserBilling|UserShipping $entity,
        ?string $street,
        ?string $postalCode,
        ?string $city,
    ): void {
        $entity->setStreet($street ?? '')
            ->setPostalCode($postalCode ?? '')
            ->setCity($city ?? '');
    }
}
