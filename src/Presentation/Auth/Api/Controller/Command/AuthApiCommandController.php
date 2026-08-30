<?php

declare(strict_types=1);

namespace App\Presentation\Auth\Api\Controller\Command;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Domain\Auth\Payload\LoginPayload;

use App\Core\Ports\{
    Auth\Handler\Command\LoginHandlerContract,
    Auth\Handler\Command\LogoutHandlerContract,
    Auth\Handler\Command\RefreshTokenHandlerContract,
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
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
     * @param ValidatorInterface $validator
    */
    public function __construct(
        private readonly LoginHandlerContract $loginHandler,
        private readonly RefreshTokenHandlerContract $refreshTokenHandler,
        private readonly LogoutHandlerContract $logoutHandler,
        private readonly RefreshTokenCookieManager $refreshTokenCookieManager,
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
    */
    public function login(Request $request): JsonResponse
    {
        return $this->handleCommand(function () use ($request) {
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
    */
    public function refresh(Request $request): JsonResponse
    {
        return $this->handleCommand(function () use ($request) {
            $refreshToken = $request->cookies->getString(self::REFRESH_TOKEN_COOKIE) ?: null;

            $result = $this->refreshTokenHandler->handle($refreshToken);

            return $this->buildTokenResponse($result, $request->isSecure());
        });
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
    */
    public function logout(Request $request): JsonResponse
    {
        return $this->handleCommand(function () use ($request) {
            $refreshToken = $request->cookies->getString(self::REFRESH_TOKEN_COOKIE) ?: null;

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
