<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Service\Command;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Entity\UserBilling,
    Segment\User\Entity\UserShipping,
    Segment\User\Payload\ProfilePayload
};

use App\Core\Application\Segment\User\Utils\NameFormatter;

use App\Core\Ports\{
    Segment\User\Service\Command\UserAddressCommandContract,
    Segment\User\Service\Command\ProfileCommandContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class ProfileCommandService implements ProfileCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param UserAddressCommandContract $userAddressCommand
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private UserAddressCommandContract $userAddressCommand,
    ) {}

    /**
     * @param User $user
     * @param ProfilePayload $payload
     *
     * @return void
    */
    public function updateProfile(User $user, ProfilePayload $payload): void
    {
        $this->updateUserBasicInfo($user, $payload);
        $this->processUserAddresses($user, $payload);

        $this->entityPersistence->persist($user, true);
    }

    /**
     * @param User $user
     * @param ProfilePayload $payload
     *
     * @return void
    */
    private function updateUserBasicInfo(User $user, ProfilePayload $payload): void
    {
        $firstName = NameFormatter::formatName($payload->firstName);
        $lastName = NameFormatter::formatName($payload->lastName);

        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setUseShipping($payload->useShipping);
        $user->setUpdatedAt();
    }

    /**
     * @param User $user
     * @param ProfilePayload $payload
     *
     * @return void
    */
    private function processUserAddresses(User $user, ProfilePayload $payload): void
    {
        $this->userAddressCommand->processAddressEntity(
            $user,
            $user->getBilling(),
            $payload->billing,
            UserBilling::class,
        );

        $this->userAddressCommand->processAddressEntity(
            $user,
            $user->getShipping(),
            $payload->shipping,
            UserShipping::class,
        );
    }
}
