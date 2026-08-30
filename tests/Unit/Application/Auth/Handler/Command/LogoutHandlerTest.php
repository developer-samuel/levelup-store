<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Auth\Handler\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Application\Auth\Handler\Command\LogoutHandler;

use App\Core\Ports\{
    Auth\Handler\Command\LogoutHandlerContract,
    Auth\Service\Command\LogoutCommandContract,
    Shared\Logging\AppLoggerContract
};

/**
 * @coversDefaultClass \App\Core\Application\Auth\Handler\Command\LogoutHandler
*/
class LogoutHandlerTest extends TestCase
{
    private const TOKEN = 'some-token';

    private LogoutCommandContract&MockObject $logoutCommand;
    private AppLoggerContract&MockObject $logger;
    private LogoutHandler $handler;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initHandler();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(LogoutHandlerContract::class, $this->handler);
    }

    public function testHandleReturnsSuccessStatus(): void
    {
        $result = $this->handler->handle(self::TOKEN);

        $this->assertSame('success', $result['status']);
    }

    public function testHandleReturnsSuccessMessage(): void
    {
        $result = $this->handler->handle(self::TOKEN);

        $this->assertSame('Logged out successfully', $result['message']);
    }

    public function testHandleDelegatesToLogoutCommand(): void
    {
        $this->logoutCommand
            ->expects($this->once())
            ->method('execute')
            ->with(self::TOKEN);

        $this->handler->handle(self::TOKEN);
    }

    public function testHandlePassesNullTokenToCommand(): void
    {
        $this->logoutCommand
            ->expects($this->once())
            ->method('execute')
            ->with(null);

        $this->handler->handle(null);
    }

    public function testHandleReturnsErrorWhenCommandThrows(): void
    {
        $this->logoutCommand
            ->method('execute')
            ->willThrowException(new \DomainException('Something went wrong'));

        $result = $this->handler->handle(self::TOKEN);

        $this->assertSame('error', $result['status']);
        $this->assertSame(422, $result['code']);
    }

    private function initMocks(): void
    {
        $this->logoutCommand = $this->createMock(LogoutCommandContract::class);
        $this->logger = $this->createMock(AppLoggerContract::class);
    }

    private function initHandler(): void
    {
        $this->handler = new LogoutHandler(
            $this->logoutCommand,
            $this->logger,
        );
    }
}
