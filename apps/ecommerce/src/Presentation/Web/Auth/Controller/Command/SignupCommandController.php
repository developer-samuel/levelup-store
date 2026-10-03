<?php

declare(strict_types=1);

namespace App\Presentation\Web\Auth\Controller\Command;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Domain\Auth\Payload\SignupPayload;

use App\Core\Ports\{
    Auth\Handler\Command\SignupHandlerContract,
    Gateways\External\Turnstile\TurnstileGatewayContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Shared\Processor\RequestProcessor,
    Shared\Manager\RefreshTokenCookieManager,
    Shared\Responder\HttpResponder,
    Web\Auth\Request\SignupRequest
};

final class SignupCommandController extends AbstractCrudCommandController
{
    public function __construct(
        private readonly SignupHandlerContract $signupHandler,
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

    public function store(Request $request): JsonResponse
    {
        return $this->handleCommand(function () use ($request) {
            $turnstileToken = $request->request->getString('cf-turnstile-response');

            if (!$this->turnstile->verify($turnstileToken, (string) $request->getClientIp())) {
                return HttpResponder::unprocessableEntity(['turnstile' => 'Bot verification failed. Please try again.']);
            }

            $signupRequest = SignupRequest::fromHttpRequest($request, $this->csrfTokenManager);

            $validationResponse = RequestProcessor::process($signupRequest, $this->validator);
            if ($validationResponse !== null) {
                return $validationResponse;
            }

            $result = $this->handleStore($signupRequest);
            $response = HttpResponder::successWithRedirect($result);

            $this->refreshTokenCookieManager->attach($result, $response, $request->isSecure());

            return $response;
        });
    }

    /** @return array<string, mixed> */
    private function handleStore(SignupRequest $request): array
    {
        $payload = $this->createPayload($request);

        return $this->signupHandler->handle($payload);
    }

    private function createPayload(SignupRequest $request): SignupPayload
    {
        return new SignupPayload(
            email: (string) $request->email,
            firstName: $request->first_name,
            lastName: $request->last_name,
            password: (string) $request->password,
            passwordConfirmation: (string) $request->password_confirmation,
        );
    }
}
