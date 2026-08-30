<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Auth\Handler\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\Auth\ValueObject\JwtTokenObject;

use App\Core\Application\Auth\Handler\Command\RefreshTokenHandler;

use App\Core\Ports\{
    Auth\Handler\Command\RefreshTokenHandlerContract,
    Auth\Service\Command\RefreshTokenCommandContract,
    Shared\Logging\AppLoggerContract
};

/**
 * @coversDefaultClass \App\Core\Application\Auth\Handler\Command\RefreshTokenHandler
*/
class RefreshTokenHandlerTest extends TestCase
{
    private RefreshTokenCommandContract&MockObject $refreshTokenCommand;
    private AppLoggerContract&MockObject $logger;
    private RefreshTokenHandler $handler;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initHandler();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(RefreshTokenHandlerContract::class, $this->handler);
    }

    public function testHandleReturnsErrorWhenTokenIsNull(): void
    {
        $result = $this->handler->handle(null);

        $this->assertUnauthenticated($result);
    }

    public function testHandleReturnsErrorWhenTokenIsEmptyString(): void
    {
        $result = $this->handler->handle('');

        $this->assertUnauthenticated($result);
    }

    public function testHandleReturnsSuccessStatus(): void
    {
        $result = $this->handleWithValidToken();

        $this->assertSame('success', $result['status']);
    }

    public function testHandleReturnsAccessToken(): void
    {
        /** @var array{data: array<string, mixed>} $result */
        $result = $this->handleWithValidToken();

        $this->assertSame('access-abc', $result['data']['access_token']);
    }

    public function testHandleReturnsRefreshToken(): void
    {
        $result = $this->handleWithValidToken();

        $this->assertSame('refresh-xyz', $result['refresh_token']);
    }

    public function testHandleDelegatesToRefreshTokenCommand(): void
    {
        $this->refreshTokenCommand
            ->expects($this->once())
            ->method('execute')
            ->with('valid-token')
            ->willReturn(new JwtTokenObject('access-abc', 'refresh-xyz'));

        $this->handler->handle('valid-token');
    }

    public function testHandleReturnsErrorWhenCommandThrows(): void
    {
        $this->refreshTokenCommand
            ->method('execute')
            ->willThrowException(new \DomainException('Token expired'));

        $result = $this->handler->handle('expired-token');

        $this->assertSame('error', $result['status']);
        $this->assertSame(422, $result['code']);
    }

    private function initMocks(): void
    {
        $this->refreshTokenCommand = $this->createMock(RefreshTokenCommandContract::class);
        $this->logger = $this->createMock(AppLoggerContract::class);
    }

    private function initHandler(): void
    {
        $this->handler = new RefreshTokenHandler(
            $this->refreshTokenCommand,
            $this->logger,
        );
    }

    /**
     * @return array<string, mixed>
    */
    private function handleWithValidToken(): array
    {
        $this->refreshTokenCommand
            ->method('execute')
            ->willReturn(new JwtTokenObject('access-abc', 'refresh-xyz'));

        return $this->handler->handle('valid-token');
    }

    /**
     * @param array<string, mixed> $result
    */
    private function assertUnauthenticated(array $result): void
    {
        $this->assertSame('error', $result['status']);
        $this->assertSame(401, $result['code']);
    }
}
