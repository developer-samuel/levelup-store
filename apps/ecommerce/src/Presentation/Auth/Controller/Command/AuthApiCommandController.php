<?php

declare(strict_types=1);

namespace App\Presentation\Auth\Controller\Command;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use OpenApi\Attributes as OA;

use App\Core\Domain\Auth\Payload\LoginPayload;

use App\Core\Ports\{
    Auth\Handler\Command\LoginHandlerContract,
    Auth\Handler\Command\LogoutHandlerContract,
    Auth\Handler\Command\RefreshTokenHandlerContract,
    Gateways\External\Turnstile\TurnstileGatewayContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Auth\Manager\RefreshTokenCookieManager,
    Auth\Request\LoginRequest,
    Shared\Responder\HttpResponder
};

final class AuthApiCommandController extends AbstractCrudCommandController
{
    private const REFRESH_TOKEN_COOKIE = 'refresh_token';

    /**
     * @param LoginHandlerContract $loginHandler
     * @param RefreshTokenHandlerContract $refreshTokenHandler
     * @param LogoutHandlerContract $logoutHandler
     * @param RefreshTokenCookieManager $refreshTokenCookieManager
     * @param TurnstileGatewayContract $turnstile
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
     * @param ValidatorInterface $validator
    */
    public function __construct(
        private readonly LoginHandlerContract $loginHandler,
        private readonly RefreshTokenHandlerContract $refreshTokenHandler,
        private readonly LogoutHandlerContract $logoutHandler,
        private readonly RefreshTokenCookieManager $refreshTokenCookieManager,
        private readonly TurnstileGatewayContract $turnstile,
        CsrfTokenManagerInterface $csrfTokenManager,
        AppLoggerContract $logger,
        ValidatorInterface $validator,
    ) {
        parent::__construct(
            $csrfTokenManager,
            $logger,
            $validator,
        );
    }

    #[OA\Post(
        path: '/api/auth/login',
        summary: 'Login with email and password',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email'),
                new OA\Property(property: 'password', type: 'string', format: 'password'),
                new OA\Property(property: 'cf_turnstile_response', type: 'string'),
                new OA\Property(property: '_csrf_token', type: 'string'),
            ]),
        ),
        tags: ['Auth'],
        security: [],
        responses: [
            new OA\Response(response: 200, description: 'JWT token issued'),
            new OA\Response(response: 422, description: 'Validation error or turnstile failed'),
        ],
    )]
    public function login(Request $request): JsonResponse
    {
        return $this->handleCommand(function () use ($request) {
            $decoded = json_decode($request->getContent(), true);
            $raw = is_array($decoded) ? ($decoded['cf_turnstile_response'] ?? '') : '';
            $turnstileToken = is_string($raw) ? $raw : '';

            if (!$this->turnstile->verify($turnstileToken, (string) $request->getClientIp())) {
                return HttpResponder::unprocessableEntity(['turnstile' => 'Bot verification failed. Please try again.']);
            }

            $loginRequest = LoginRequest::fromHttpRequest(
                $request,
                $this->csrfTokenManager,
            );

            $errors = $loginRequest->errors($this->validator);
            if ($errors !== []) {
                return HttpResponder::unprocessableEntity($errors);
            }

            $result = $this->handleLogin($loginRequest);

            return $this->buildTokenResponse($result, $request->isSecure());
        });
    }

    #[OA\Post(
        path: '/api/auth/refresh',
        summary: 'Refresh JWT using refresh_token cookie',
        tags: ['Auth'],
        security: [],
        responses: [
            new OA\Response(response: 200, description: 'New JWT token issued'),
            new OA\Response(response: 401, description: 'Invalid or missing refresh token'),
        ],
    )]
    public function refresh(Request $request): JsonResponse
    {
        return $this->handleCommand(function () use ($request) {
            $token = $request->cookies->getString(self::REFRESH_TOKEN_COOKIE);
            $refreshToken = $token !== '' ? $token : null;

            $result = $this->refreshTokenHandler->handle($refreshToken);

            return $this->buildTokenResponse($result, $request->isSecure());
        });
    }

    #[OA\Post(
        path: '/api/auth/logout',
        summary: 'Logout and invalidate refresh token',
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Logged out successfully'),
        ],
    )]
    public function logout(Request $request): JsonResponse
    {
        return $this->handleCommand(function () use ($request) {
            $token = $request->cookies->getString(self::REFRESH_TOKEN_COOKIE);
            $refreshToken = $token !== '' ? $token : null;

            $result = $this->logoutHandler->handle($refreshToken);

            return $this->clearTokenResponse($result, $request->isSecure());
        });
    }

    /**
     * @param LoginRequest $request
     *
     * @return array<string, mixed>
    */
    private function handleLogin(LoginRequest $request): array
    {
        $payload = new LoginPayload(
            email:    $request->email,
            password: $request->password,
        );

        return $this->loginHandler->handle($payload);
    }

    /**
     * @param array<string, mixed> $result
     * @param bool $secure
     *
     * @return JsonResponse
    */
    private function buildTokenResponse(array $result, bool $secure): JsonResponse
    {
        $refreshToken = $result[self::REFRESH_TOKEN_COOKIE] ?? null;
        unset($result[self::REFRESH_TOKEN_COOKIE]);

        $response = HttpResponder::success($result);

        if (is_string($refreshToken)) {
            $response->headers->setCookie($this->refreshTokenCookieManager->create($refreshToken, $secure));
        }

        return $response;
    }

    /**
     * @param array<string, mixed> $result
     * @param bool $secure
     *
     * @return JsonResponse
    */
    private function clearTokenResponse(array $result, bool $secure): JsonResponse
    {
        $response = HttpResponder::success($result);
        $this->refreshTokenCookieManager->clear($response->headers, $secure);

        return $response;
    }
}
