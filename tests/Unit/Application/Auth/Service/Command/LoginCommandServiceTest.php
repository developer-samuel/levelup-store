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

use App\Core\Application\Auth\Service\Command\LoginCommandService;

use App\Core\Ports\{
    Auth\Repository\RefreshTokenRepositoryContract,
    Auth\Service\Command\LoginCommandContract,
    Gateways\External\Jwt\JwtGatewayContract
};

/**
 * @coversDefaultClass \App\Core\Application\Auth\Service\Command\LoginCommandService
*/
final class LoginCommandServiceTest extends TestCase
{
    private JwtGatewayContract&MockObject $jwtGateway;
    private RefreshTokenRepositoryContract&MockObject $refreshTokenRepository;
    private LoginCommandService $service;
    private User&MockObject $user;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();

        $this->user = $this->createMock(User::class);
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(LoginCommandContract::class, $this->service);
    }

    public function testExecuteReturnsJwtTokenObject(): void
    {
        $result = $this->executeForUser();

        $this->assertInstanceOf(JwtTokenObject::class, $result);
    }

    public function testExecuteReturnsCorrectAccessToken(): void
    {
        $result = $this->executeForUser(accessToken: 'access-token-abc');

        $this->assertSame('access-token-abc', $result->accessToken);
    }

    public function testExecuteReturnsCorrectRefreshToken(): void
    {
        $result = $this->executeForUser(refreshToken: 'refresh-token-xyz');

        $this->assertSame('refresh-token-xyz', $result->refreshToken);
    }

    public function testExecuteGeneratesAccessTokenForGivenUser(): void
    {
        $this->jwtGateway
            ->expects($this->once())
            ->method('generateAccessToken')
            ->with($this->user)
            ->willReturn('access-token-abc');

        $this->refreshTokenRepository
            ->method('create')
            ->willReturn($this->buildRefreshToken('refresh-token-xyz'));

        $this->service->execute($this->user);
    }

    public function testExecuteCreatesRefreshTokenForGivenUser(): void
    {
        $this->jwtGateway
            ->method('generateAccessToken')
            ->willReturn('access-token-abc');

        $this->refreshTokenRepository
            ->expects($this->once())
            ->method('create')
            ->with($this->user)
            ->willReturn($this->buildRefreshToken('refresh-token-xyz'));

        $this->service->execute($this->user);
    }

    private function initMocks(): void
    {
        $this->jwtGateway = $this->createMock(JwtGatewayContract::class);
        $this->refreshTokenRepository = $this->createMock(RefreshTokenRepositoryContract::class);
    }

    private function initService(): void
    {
        $this->service = new LoginCommandService(
            $this->jwtGateway,
            $this->refreshTokenRepository,
        );
    }

    private function executeForUser(
        string $accessToken = 'access-token-abc',
        string $refreshToken = 'refresh-token-xyz',
    ): JwtTokenObject {
        $this->jwtGateway
            ->method('generateAccessToken')
            ->willReturn($accessToken);

        $this->refreshTokenRepository
            ->method('create')
            ->willReturn($this->buildRefreshToken($refreshToken));

        return $this->service->execute($this->user);
    }
    
    private function buildRefreshToken(string $token): RefreshToken&MockObject
    {
        $refreshToken = $this->createMock(RefreshToken::class);
        $refreshToken->method('getToken')->willReturn($token);

        return $refreshToken;
    }
}
