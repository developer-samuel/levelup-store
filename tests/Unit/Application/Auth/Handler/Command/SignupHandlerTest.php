<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Auth\Handler\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Auth\Payload\SignupPayload,
    Auth\ValueObject\JwtTokenObject,
    Shared\Exception\TooManyRequestsException,
    Segment\User\Entity\User
};

use App\Core\Application\Auth\Handler\Command\SignupHandler;

use App\Core\Ports\{
    Auth\Service\Command\LoginCommandContract,
    Auth\Service\Command\SignupCommandContract,
    Auth\Service\Command\VerificationCommandContract,
    Auth\Service\Query\LoginRedirectQueryContract,
    Segment\Audit\AuditLoggerContract,
    Shared\Logging\AppLoggerContract,
    Shared\RateLimiter\RateLimiterContract
};

/**
 * @coversDefaultClass \App\Core\Application\Auth\Handler\Command\SignupHandler
*/
final class SignupHandlerTest extends TestCase
{
    private SignupCommandContract&MockObject $signupCommand;
    private VerificationCommandContract&MockObject $verificationCommand;
    private LoginCommandContract&MockObject $loginCommand;
    private LoginRedirectQueryContract&MockObject $loginRedirectQuery;
    private RateLimiterContract&MockObject $rateLimiter;
    private AuditLoggerContract&MockObject $audit;
    private AppLoggerContract&MockObject $logger;
    private SignupHandler $handler;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initHandler();
    }

    public function testHandleReturnsSuccessStatus(): void
    {
        $result = $this->handleSuccessfully();

        self::assertSame('success', $result['status']);
    }

    public function testHandleReturnsAccessToken(): void
    {
        /** @var array{data: array<string, mixed>} $result */
        $result = $this->handleSuccessfully(accessToken: 'access-abc');

        self::assertSame('access-abc', $result['data']['access_token']);
    }

    public function testHandleReturnsRefreshToken(): void
    {
        $result = $this->handleSuccessfully(refreshToken: 'refresh-xyz');

        self::assertSame('refresh-xyz', $result['refresh_token']);
    }

    public function testHandleReturnsRedirectRoute(): void
    {
        /** @var array{data: array<string, mixed>} $result */
        $result = $this->handleSuccessfully(redirectRoute: '/dashboard');

        self::assertSame('/dashboard', $result['data']['redirect']);
    }

    public function testHandleCallsSignupCommand(): void
    {
        $this->signupCommand
            ->expects(self::once())
            ->method('signup');

        $this->setupSuccess();

        $this->handler->handle($this->buildPayload());
    }

    public function testHandleSendsVerificationEmail(): void
    {
        $this->verificationCommand
            ->expects(self::once())
            ->method('createAndSaveTokenForUser');

        $this->setupSuccess();

        $this->handler->handle($this->buildPayload());
    }

    public function testHandleCallsLoginCommandForCreatedUser(): void
    {
        $user = $this->createMock(User::class);

        $this->signupCommand->method('signup')->willReturn($user);

        $this->loginCommand
            ->expects(self::once())
            ->method('execute')
            ->with($user)
            ->willReturn(new JwtTokenObject('access-abc', 'refresh-xyz'));

        $this->verificationCommand->method('createAndSaveTokenForUser');
        $this->loginRedirectQuery->method('getRedirectRoute')->willReturn('/dashboard');

        $this->handler->handle($this->buildPayload());
    }

    public function testHandleReturnsRateLimitErrorWhenTooManyRequests(): void
    {
        $this->rateLimiter
            ->method('track')
            ->willThrowException(new TooManyRequestsException(60));

        $result = $this->handler->handle($this->buildPayload());

        self::assertSame('error', $result['status']);
        self::assertSame(429, $result['code']);
    }

    public function testHandleLogsErrorWhenLogicExceptionThrown(): void
    {
        $this->signupCommand
            ->method('signup')
            ->willThrowException(new \LogicException('Unexpected logic failure.'));

        $this->logger
            ->expects(self::once())
            ->method('error');

        $this->handler->handle($this->buildPayload());
    }

    public function testHandleReturnsErrorWhenSignupCommandThrows(): void
    {
        $this->signupCommand
            ->method('signup')
            ->willThrowException(new \DomainException('Email already taken'));

        $result = $this->handler->handle($this->buildPayload());

        self::assertSame('error', $result['status']);
        self::assertSame(422, $result['code']);
    }

    private function initMocks(): void
    {
        $this->signupCommand = $this->createMock(SignupCommandContract::class);
        $this->verificationCommand = $this->createMock(VerificationCommandContract::class);
        $this->loginCommand = $this->createMock(LoginCommandContract::class);
        $this->loginRedirectQuery = $this->createMock(LoginRedirectQueryContract::class);
        $this->rateLimiter = $this->createMock(RateLimiterContract::class);
        $this->audit = $this->createMock(AuditLoggerContract::class);
        $this->logger = $this->createMock(AppLoggerContract::class);
    }

    private function initHandler(): void
    {
        $this->handler = new SignupHandler(
            $this->signupCommand,
            $this->verificationCommand,
            $this->loginCommand,
            $this->loginRedirectQuery,
            $this->rateLimiter,
            $this->audit,
            $this->logger,
        );
    }

    /**
     * @return array<string, mixed>
    */
    private function handleSuccessfully(
        string $accessToken = 'access-abc',
        string $refreshToken = 'refresh-xyz',
        string $redirectRoute = '/dashboard',
    ): array {
        $this->setupSuccess($accessToken, $refreshToken, $redirectRoute);

        return $this->handler->handle($this->buildPayload());
    }

    private function setupSuccess(
        string $accessToken = 'access-abc',
        string $refreshToken = 'refresh-xyz',
        string $redirectRoute = '/dashboard',
    ): void {
        $user = $this->createMock(User::class);

        $this->signupCommand->method('signup')->willReturn($user);
        $this->verificationCommand->method('createAndSaveTokenForUser');

        $this->loginCommand->method('execute')->willReturn(
            new JwtTokenObject($accessToken, $refreshToken),
        );

        $this->loginRedirectQuery->method('getRedirectRoute')->willReturn($redirectRoute);
    }

    private function buildPayload(): SignupPayload
    {
        return new SignupPayload(
            email:                'test@example.com',
            firstName:            'Test',
            lastName:             'User',
            password:             'secret',
            passwordConfirmation: 'secret',
        );
    }
}
