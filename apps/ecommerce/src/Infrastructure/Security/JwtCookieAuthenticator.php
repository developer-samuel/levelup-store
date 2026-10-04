<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\HttpFoundation\Response,
    Component\Security\Core\Authentication\Token\TokenInterface,
    Component\Security\Core\Exception\AuthenticationException,
    Component\Security\Core\Exception\CustomUserMessageAuthenticationException,
    Component\Security\Http\Authenticator\AbstractAuthenticator,
    Component\Security\Http\Authenticator\Passport\Badge\UserBadge,
    Component\Security\Http\Authenticator\Passport\Passport,
    Component\Security\Http\Authenticator\Passport\SelfValidatingPassport,
    Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface
};

use App\Core\Ports\Auth\Repository\RefreshTokenRepositoryContract;

final class JwtCookieAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private const COOKIE_NAME = 'refresh_token';

    public function __construct(
        private readonly RefreshTokenRepositoryContract $refreshTokenRepository,
    ) {}

    public function supports(Request $request): bool
    {
        return $request->cookies->has(self::COOKIE_NAME);
    }

    public function authenticate(Request $request): Passport
    {
        $rawToken = $request->cookies->getString(self::COOKIE_NAME);

        $refreshToken = $this->refreshTokenRepository->findByToken($rawToken);

        if ($refreshToken === null || $refreshToken->isExpired() || $refreshToken->getUser()->isDeleted()) {
            throw new CustomUserMessageAuthenticationException('Invalid or expired session.');
        }

        return new SelfValidatingPassport(
            new UserBadge($refreshToken->getUser()->getEmail()),
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return null;
        }

        return new JsonResponse([
            'code'    => 401,
            'message' => $exception->getMessageKey(),
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new JsonResponse([
            'code'    => 401,
            'message' => 'Authentication required.',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
