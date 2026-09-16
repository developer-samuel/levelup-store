<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Auth\Service\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\Auth\Entity\RefreshToken;

use App\Core\Application\Auth\Service\Command\LogoutCommandService;

use App\Core\Ports\{
    Auth\Repository\RefreshTokenRepositoryContract,
    Auth\Service\Command\LogoutCommandContract,
    Gateways\Internal\Auth\TokenBlacklistContract
};

/**
 * @coversDefaultClass \App\Core\Application\Auth\Service\Command\LogoutCommandService
*/
final class LogoutCommandServiceTest extends TestCase
{
    private RefreshTokenRepositoryContract&MockObject $refreshTokenRepository;
    private TokenBlacklistContract&MockObject $tokenBlacklist;
    private LogoutCommandService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(LogoutCommandContract::class, $this->service);
    }

    public function testExecuteDoesNothingWhenTokenIsNull(): void
    {
        $this->assertRepositorySkipped(null);
    }

    public function testExecuteDoesNothingWhenTokenIsEmptyString(): void
    {
        $this->assertRepositorySkipped('');
    }

    public function testExecuteDoesNothingWhenTokenNotFound(): void
    {
        $this->refreshTokenRepository
            ->method('findByToken')
            ->willReturn(null);

        $this->refreshTokenRepository
            ->expects($this->never())
            ->method('revoke');

        $this->service->execute('unknown-token');
    }

    public function testExecuteFindsTokenByValue(): void
    {
        $this->refreshTokenRepository
            ->expects($this->once())
            ->method('findByToken')
            ->with('valid-token')
            ->willReturn(null);

        $this->service->execute('valid-token');
    }

    public function testExecuteRevokesTokenWhenFound(): void
    {
        $token = $this->createMock(RefreshToken::class);
        $token->method('getExpiresAt')->willReturn(new \DateTimeImmutable('+30 days'));

        $this->refreshTokenRepository
            ->method('findByToken')
            ->willReturn($token);

        $this->refreshTokenRepository
            ->expects($this->once())
            ->method('revoke')
            ->with($token);

        $this->service->execute('valid-token');
    }

    public function testExecuteBlacklistsTokenAfterRevoke(): void
    {
        $expiresAt = new \DateTimeImmutable('+30 days');
        $token     = $this->createMock(RefreshToken::class);
        $token->method('getExpiresAt')->willReturn($expiresAt);

        $this->refreshTokenRepository
            ->method('findByToken')
            ->willReturn($token);

        $this->tokenBlacklist
            ->expects($this->once())
            ->method('blacklist')
            ->with('valid-token', $expiresAt);

        $this->service->execute('valid-token');
    }

    private function initMocks(): void
    {
        $this->refreshTokenRepository = $this->createMock(RefreshTokenRepositoryContract::class);
        $this->tokenBlacklist         = $this->createMock(TokenBlacklistContract::class);
    }

    private function initService(): void
    {
        $this->service = new LogoutCommandService(
            $this->refreshTokenRepository,
            $this->tokenBlacklist,
        );
    }

    private function assertRepositorySkipped(?string $token): void
    {
        $this->refreshTokenRepository
            ->expects($this->never())
            ->method('findByToken');

        $this->service->execute($token);
    }
}
