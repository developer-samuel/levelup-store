<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Auth\Handler\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Auth\Payload\LoginPayload,
    Auth\ValueObject\JwtTokenObject,
    Shared\Exception\TooManyRequestsException,
    Segment\User\Entity\User
};

use App\Core\Application\Auth\Handler\Command\LoginHandler;

use App\Core\Ports\{
    Auth\Handler\Command\LoginHandlerContract,
    Auth\Service\Command\LoginCommandContract,
    Auth\Service\Query\LoginRedirectQueryContract,
    Security\Provider\PasswordHasherProviderContract,
    Segment\Audit\AuditLoggerContract,
    Segment\User\Repository\UserRepositoryContract,
    Shared\Logging\AppLoggerContract,
    Shared\RateLimiter\RateLimiterContract
};

/**
 * @coversDefaultClass \App\Core\Application\Auth\Handler\Command\LoginHandler
*/
class LoginHandlerTest extends TestCase
{
    private UserRepositoryContract&MockObject $userRepository;
    private PasswordHasherProviderContract&MockObject $passwordHasherProvider;
    private LoginCommandContract&MockObject $loginCommand;
    private LoginRedirectQueryContract&MockObject $loginRedirectQuery;
    private RateLimiterContract&MockObject $rateLimiter;
    private AuditLoggerContract&MockObject $audit;
    private AppLoggerContract&MockObject $logger;
    private LoginHandler $handler;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initHandler();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(LoginHandlerContract::class, $this->handler);
    }

    public function testHandleReturnsSuccessStatus(): void
    {
        $this->setupSuccess();

        $result = $this->handler->handle($this->buildPayload());

        $this->assertSame('success', $result['status']);
    }

    public function testHandleReturnsAccessToken(): void
    {
        $this->setupSuccess(accessToken: 'access-abc');

        /** @var array{data: array<string, mixed>} $result */
        $result = $this->handler->handle($this->buildPayload());

        $this->assertSame('access-abc', $result['data']['access_token']);
    }

    public function testHandleReturnsRefreshToken(): void
    {
        $this->setupSuccess(refreshToken: 'refresh-xyz');

        $result = $this->handler->handle($this->buildPayload());

        $this->assertSame('refresh-xyz', $result['refresh_token']);
    }

    public function testHandleReturnsRedirectRoute(): void
    {
        $this->setupSuccess(redirectRoute: '/dashboard');

        /** @var array{data: array<string, mixed>} $result */
        $result = $this->handler->handle($this->buildPayload());

        $this->assertSame('/dashboard', $result['data']['redirect']);
    }

    public function testHandleCallsLoginCommandForValidUser(): void
    {
        $user = $this->createMock(User::class);

        $this->userRepository->method('findByEmail')->willReturn($user);
        $this->passwordHasherProvider->method('isPasswordValid')->willReturn(true);

        $this->loginCommand
            ->expects($this->once())
            ->method('execute')
            ->with($user)
            ->willReturn(new JwtTokenObject('access-abc', 'refresh-xyz'));

        $this->loginRedirectQuery->method('getRedirectRoute')->willReturn('/dashboard');

        $this->handler->handle($this->buildPayload());
    }

    public function testHandleReturnsErrorWhenUserNotFound(): void
    {
        $this->userRepository->method('findByEmail')->willReturn(null);

        $result = $this->handler->handle($this->buildPayload());

        $this->assertInvalidCredentials($result);
    }

    public function testHandleReturnsErrorWhenPasswordInvalid(): void
    {
        $user = $this->createMock(User::class);

        $this->userRepository->method('findByEmail')->willReturn($user);
        $this->passwordHasherProvider->method('isPasswordValid')->willReturn(false);

        $result = $this->handler->handle($this->buildPayload());

        $this->assertInvalidCredentials($result);
    }

    public function testHandleReturnsRateLimitErrorWhenTooManyRequests(): void
    {
        $this->rateLimiter
            ->method('track')
            ->willThrowException(new TooManyRequestsException(60));

        $result = $this->handler->handle($this->buildPayload());

        $this->assertSame('error', $result['status']);
        $this->assertSame(429, $result['code']);
    }

    private function initMocks(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryContract::class);
        $this->passwordHasherProvider = $this->createMock(PasswordHasherProviderContract::class);
        $this->loginCommand = $this->createMock(LoginCommandContract::class);
        $this->loginRedirectQuery = $this->createMock(LoginRedirectQueryContract::class);
        $this->rateLimiter = $this->createMock(RateLimiterContract::class);
        $this->audit = $this->createMock(AuditLoggerContract::class);
        $this->logger = $this->createMock(AppLoggerContract::class);
    }

    private function initHandler(): void
    {
        $this->handler = new LoginHandler(
            $this->userRepository,
            $this->passwordHasherProvider,
            $this->loginCommand,
            $this->loginRedirectQuery,
            $this->rateLimiter,
            $this->audit,
            $this->logger,
        );
    }

    private function setupSuccess(
        string $accessToken = 'access-abc',
        string $refreshToken = 'refresh-xyz',
        string $redirectRoute = '/dashboard',
    ): void {
        $user = $this->createMock(User::class);

        $this->userRepository->method('findByEmail')->willReturn($user);
        $this->passwordHasherProvider->method('isPasswordValid')->willReturn(true);
        $this->loginCommand->method('execute')->willReturn(new JwtTokenObject($accessToken, $refreshToken));
        $this->loginRedirectQuery->method('getRedirectRoute')->willReturn($redirectRoute);
    }

    private function buildPayload(
        string $email = 'test@example.com',
        string $password = 'secret',
    ): LoginPayload {
        return new LoginPayload(
            email:    $email,
            password: $password,
        );
    }

    /**
     * @param array<string, mixed> $result
    */
    private function assertInvalidCredentials(array $result): void
    {
        $this->assertSame('error', $result['status']);
        $this->assertSame(422, $result['code']);
        $this->assertSame('Invalid credentials.', $result['message']);
    }
}
