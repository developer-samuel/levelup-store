<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Auth\Service\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Auth\Payload\SignupPayload,
    Segment\User\Entity\User,
    Segment\User\Enum\UserRole
};

use App\Core\Application\{
    Auth\Service\Command\SignupCommandService,
    Segment\User\Utils\NameFormatter
};

use App\Core\Ports\{
    Auth\Service\Command\SignupCommandContract,
    Security\Provider\PasswordHasherProviderContract,
    Shared\Persistence\EntityPersistenceContract
};

/**
 * @coversDefaultClass \App\Core\Application\Auth\Service\Command\SignupCommandService
*/
final class SignupCommandServiceTest extends TestCase
{
    private EntityPersistenceContract&MockObject $entityPersistence;
    private PasswordHasherProviderContract&MockObject $passwordHasherProvider;
    private SignupCommandService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(SignupCommandContract::class, $this->service);
    }

    public function testSignupReturnsUser(): void
    {
        $result = $this->signup();

        $this->assertInstanceOf(User::class, $result);
    }

    public function testSignupSetsEmail(): void
    {
        $result = $this->signup($this->buildPayload(email: 'test@example.com'));

        $this->assertSame('test@example.com', $result->getEmail());
    }

    public function testSignupSetsFormattedFirstName(): void
    {
        $result = $this->signup($this->buildPayload(firstName: 'john'));

        $this->assertSame(NameFormatter::formatName('john'), $result->getFirstName());
    }

    public function testSignupSetsFormattedLastName(): void
    {
        $result = $this->signup($this->buildPayload(lastName: 'doe'));

        $this->assertSame(NameFormatter::formatName('doe'), $result->getLastName());
    }

    public function testSignupSetsUserRole(): void
    {
        $result = $this->signup();

        $this->assertSame(UserRole::USER, $result->getRole());
    }

    public function testSignupSetsHashedPassword(): void
    {
        $this->passwordHasherProvider->method('hash')->willReturn('hashed-secret');

        $result = $this->service->signup($this->buildPayload());

        $this->assertSame('hashed-secret', $result->getPassword());
    }

    public function testSignupHashesPasswordForCreatedUser(): void
    {
        $this->passwordHasherProvider
            ->expects($this->once())
            ->method('hash')
            ->with($this->isInstanceOf(User::class), 'plain-password')
            ->willReturn('hashed');

        $this->service->signup($this->buildPayload(password: 'plain-password'));
    }

    public function testSignupPersistsUserWithFlush(): void
    {
        $this->passwordHasherProvider->method('hash')->willReturn('hashed');

        $this->entityPersistence
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class), true);

        $this->service->signup($this->buildPayload());
    }

    private function initMocks(): void
    {
        $this->entityPersistence = $this->createMock(EntityPersistenceContract::class);
        $this->passwordHasherProvider = $this->createMock(PasswordHasherProviderContract::class);
    }

    private function initService(): void
    {
        $this->service = new SignupCommandService(
            $this->entityPersistence,
            $this->passwordHasherProvider,
        );
    }

    private function signup(?SignupPayload $payload = null): User
    {
        $this->passwordHasherProvider->method('hash')->willReturn('hashed');

        return $this->service->signup($payload ?? $this->buildPayload());
    }

    private function buildPayload(
        string $email = 'test@example.com',
        string $firstName = 'Test',
        string $lastName = 'User',
        string $password = 'plain-password',
    ): SignupPayload {
        return new SignupPayload(
            email: $email,
            firstName: $firstName,
            lastName: $lastName,
            password: $password,
            passwordConfirmation: $password,
        );
    }
}
