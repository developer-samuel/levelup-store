<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Service\Command;

use App\Core\Domain\{
    Auth\Payload\SignupPayload,
    Segment\User\Entity\User,
    Segment\User\Enum\UserRole
};

use App\Core\Application\Segment\User\Utils\NameFormatter;

use App\Core\Ports\{
    Auth\Service\Command\SignupCommandContract,
    Security\Provider\PasswordHasherProviderContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class SignupCommandService implements SignupCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param PasswordHasherProviderContract $passwordHasherProvider
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private PasswordHasherProviderContract $passwordHasherProvider,
    ) {}

    /**
     * @param SignupPayload $payload
     *
     * @return User
    */
    public function signup(SignupPayload $payload): User
    {
        $user = $this->createUser($payload);

        $this->entityPersistence->persist($user, true);

        return $user;
    }

    /**
     * @param SignupPayload $payload
     *
     * @return User
    */
    private function createUser(SignupPayload $payload): User
    {
        $user = new User();

        $user->setEmail($payload->email)
            ->setFirstName(NameFormatter::formatName($payload->firstName))
            ->setLastName(NameFormatter::formatName($payload->lastName))
            ->setRole(UserRole::USER)
            ->setPassword(
                $this->passwordHasherProvider->hash($user, $payload->password),
            );

        return $user;
    }
}
