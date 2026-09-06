<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Auth\Service\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Auth\Entity\RefreshToken,
    Auth\ValueObject\JwtTokenObject,
    Segment\User\Entity\User
};

use App\Core\Application\Auth\Service\Command\RefreshTokenCommandService;

use App\Core\Ports\{
    Auth\Repository\RefreshTokenRepositoryContract,
    Auth\Service\Command\RefreshTokenCommandContract,
    Gateways\External\Jwt\JwtGatewayContract,
    Gateways\Internal\Auth\TokenBlacklistContract
};

/**
 * @coversDefaultClass \App\Core\Application\Auth\Service\Command\RefreshTokenCommandService
*/
final class RefreshTokenCommandServiceTest extends TestCase
{
    private JwtGatewayContract&MockObject $jwtGateway;
    private RefreshTokenRepositoryContract&MockObject $refreshTokenRepository;
    private TokenBlacklistContract&MockObject $tokenBlacklist;
    private RefreshTokenCommandService $service;
    private User&MockObject $user;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();

        $this->user = $this->createMock(User::class);
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(RefreshTokenCommandContract::class, $this->service);
    }

    public function testExecuteReturnsJwtTokenObject(): void
    {
        $result = $this->executeWithValidToken();

        $this->assertInstanceOf(JwtTokenObject::class, $result);
    }

    public function testExecuteReturnsCorrectAccessToken(): void
    {
        $result = $this->executeWithValidToken(accessToken: 'new-access-token');

        $this->assertSame('new-access-token', $result->accessToken);
    }

    public function testExecuteReturnsCorrectRefreshToken(): void
    {
        $result = $this->executeWithValidToken(newRefreshToken: 'new-refresh-token');

        $this->assertSame('new-refresh-token', $result->refreshToken);
    }

    public function testExecuteThrowsWhenTokenIsBlacklisted(): void
    {
        $this->tokenBlacklist
            ->method('isBlacklisted')
            ->willReturn(true);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessageMatches('/reuse detected/i');

        $this->service->execute('blacklisted-token');
    }

    public function testExecuteThrowsWhenTokenNotFound(): void
    {
        $this->refreshTokenRepository
            ->method('findByToken')
            ->willReturn(null);

        $this->expectException(\DomainException::class);

        $this->service->execute('non-existing-token');
    }

    public function testExecuteThrowsWhenTokenExpired(): void
    {
        $token = $this->createMock(RefreshToken::class);
        $token->method('isExpired')->willReturn(true);

        $this->refreshTokenRepository
            ->method('findByToken')
            ->willReturn($token);

        $this->expectException(\DomainException::class);

        $this->service->execute('expired-token');
    }

    public function testExecuteRevokesOldToken(): void
    {
        $token = $this->mockValidToken('old-refresh-token', $this->user);

        $this->refreshTokenRepository
            ->expects($this->once())
            ->method('revoke')
            ->with($token);

        $this->jwtGateway
            ->method('generateAccessToken')
            ->willReturn('new-access-token');

        $this->refreshTokenRepository
            ->method('create')
            ->willReturn($this->buildRefreshToken('new-refresh-token'));

        $this->service->execute('old-refresh-token');
    }

    public function testExecuteBlacklistsOldTokenAfterRotation(): void
    {
        $this->mockValidToken('old-refresh-token', $this->user);

        $this->tokenBlacklist
            ->expects($this->once())
            ->method('blacklist')
            ->with('old-refresh-token', $this->isInstanceOf(\DateTimeImmutable::class));

        $this->jwtGateway
            ->method('generateAccessToken')
            ->willReturn('new-access-token');

        $this->refreshTokenRepository
            ->method('create')
            ->willReturn($this->buildRefreshToken('new-refresh-token'));

        $this->service->execute('old-refresh-token');
    }

    public function testExecuteGeneratesNewAccessTokenForUser(): void
    {
        $this->mockValidToken('old-refresh-token', $this->user);

        $this->jwtGateway
            ->expects($this->once())
            ->method('generateAccessToken')
            ->with($this->user)
            ->willReturn('new-access-token');

        $this->refreshTokenRepository
            ->method('create')
            ->willReturn($this->buildRefreshToken('new-refresh-token'));

        $this->service->execute('old-refresh-token');
    }

    public function testExecuteCreatesNewRefreshTokenForUser(): void
    {
        $this->mockValidToken('old-refresh-token', $this->user);

        $this->jwtGateway
            ->method('generateAccessToken')
            ->willReturn('new-access-token');

        $this->refreshTokenRepository
            ->expects($this->once())
            ->method('create')
            ->with($this->user)
            ->willReturn($this->buildRefreshToken('new-refresh-token'));

        $this->service->execute('old-refresh-token');
    }

    private function initMocks(): void
    {
        $this->jwtGateway            = $this->createMock(JwtGatewayContract::class);
        $this->refreshTokenRepository = $this->createMock(RefreshTokenRepositoryContract::class);
        $this->tokenBlacklist        = $this->createMock(TokenBlacklistContract::class);
    }

    private function initService(): void
    {
        $this->service = new RefreshTokenCommandService(
            $this->jwtGateway,
            $this->refreshTokenRepository,
            $this->tokenBlacklist,
        );
    }

    private function executeWithValidToken(
        string $accessToken = 'new-access-token',
        string $newRefreshToken = 'new-refresh-token',
    ): JwtTokenObject {
        $this->mockValidToken('old-refresh-token', $this->user);

        $this->jwtGateway
            ->method('generateAccessToken')
            ->willReturn($accessToken);

        $this->refreshTokenRepository
            ->method('create')
            ->willReturn($this->buildRefreshToken($newRefreshToken));

        return $this->service->execute('old-refresh-token');
    }

    private function mockValidToken(string $tokenValue, User $user): RefreshToken&MockObject
    {
        $token = $this->createMock(RefreshToken::class);
        $token->method('isExpired')->willReturn(false);
        $token->method('getUser')->willReturn($user);
        $token->method('getExpiresAt')->willReturn(new \DateTimeImmutable('+30 days'));

        $this->refreshTokenRepository
            ->method('findByToken')
            ->with($tokenValue)
            ->willReturn($token);

        return $token;
    }

    private function buildRefreshToken(string $token): RefreshToken&MockObject
    {
        $refreshToken = $this->createMock(RefreshToken::class);
        $refreshToken->method('getToken')->willReturn($token);

        return $refreshToken;
    }
}
