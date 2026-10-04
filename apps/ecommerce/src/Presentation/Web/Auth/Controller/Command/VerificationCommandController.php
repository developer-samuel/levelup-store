<?php

declare(strict_types=1);

namespace App\Presentation\Web\Auth\Controller\Command;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Response,
    Component\Security\Csrf\CsrfTokenManagerInterface
};

use App\Presentation\Shared\Manager\RefreshTokenCookieManager;

use App\Core\Domain\Auth\Payload\UpdateVerificationPayload;

use App\Core\Ports\{
    Auth\Handler\Command\StoreVerificationHandlerContract,
    Auth\Handler\Command\UpdateVerificationHandlerContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Web\Auth\Request\VerificationRequest
};

final class VerificationCommandController extends AbstractCrudCommandController
{
    public function __construct(
        private readonly StoreVerificationHandlerContract $storeVerificationHandler,
        private readonly UpdateVerificationHandlerContract $updateVerificationHandler,
        private readonly RefreshTokenCookieManager $refreshTokenCookieManager,
        CsrfTokenManagerInterface $csrfTokenManager,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $csrfTokenManager,
            $logger,
        );
    }

    public function store(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            VerificationRequest::class,
            fn () => $this->storeVerificationHandler->handle(),
        );
    }

    public function update(Request $request): Response
    {
        $payload = new UpdateVerificationPayload(
            token: $request->query->getString('token'),
        );

        $tokenPair = $this->updateVerificationHandler->handle($payload);

        if ($tokenPair === null) {
            return $this->redirectToRoute('must_verify');
        }

        $response = $this->redirectToRoute('home');
        $response->headers->setCookie(
            $this->refreshTokenCookieManager->create($tokenPair->refreshToken, $request->isSecure()),
        );

        return $response;
    }
}
