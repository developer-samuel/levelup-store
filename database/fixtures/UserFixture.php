<?php

declare(strict_types=1);

namespace Database\Fixtures;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\{
    Bundle\FixturesBundle\FixtureGroupInterface,
    Persistence\ObjectManager
};

use Database\{
    Seeds\Abstract\AbstractFixture,
    Seeds\Factories\UserFactory,
    Seeds\Records\UserRecord
};

use App\Core\Ports\{
    Shared\Logging\AppLoggerContract,
    Shared\Logging\ConsoleLoggerContract
};

final class UserFixture extends AbstractFixture implements FixtureGroupInterface
{
    use UserFactory;

    /**
     * @param UserPasswordHasherInterface $passwordHasher
     * @param UserRecord $userRecord
     * @param AppLoggerContract $appLogger
     * @param ConsoleLoggerContract $consoleLogger
    */
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRecord $userRecord,
        AppLoggerContract $appLogger,
        ConsoleLoggerContract $consoleLogger,
    ) {
        parent::__construct(
            $appLogger,
            $consoleLogger,
        );
    }

    /**
     * @return iterable<mixed>
    */
    protected function getData(): iterable
    {
        return $this->userRecord->fetchData();
    }

    /**
     * @param array{
     *     email: string,
     *     first_name: string,
     *     last_name: string,
     *     password: string,
     *     role: string,
     * } $data
     * @param ObjectManager $manager
     *
     * @return void
    */
    protected function createEntity(mixed $data, ObjectManager $manager): void
    {
        $user = $this->createUsersFromData($data);
        $manager->persist($user);
    }
}
