<?php

declare(strict_types=1);

namespace App\Presentation\Auth\Controller\Command;

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
    Auth\Manager\RefreshTokenCookieManager,
    Auth\Request\SignupRequest,
    Shared\Processor\RequestProcessor,
    Shared\Responder\HttpResponder
};

class SignupCommandController extends AbstractCrudCommandController
{
    /**
     * @param SignupHandlerContract $signupHandler
     * @param RefreshTokenCookieManager $refreshTokenCookieManager
     * @param TurnstileGatewayContract $turnstile
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
     * @param ValidatorInterface $validator
    */
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
    */
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

    /**
     * @param SignupRequest $request
     *
     * @return array<string, mixed>
    */
    private function handleStore(SignupRequest $request): array
    {
        $payload = $this->createPayload($request);

        return $this->signupHandler->handle($payload);
    }

    /**
     * @param SignupRequest $request
     *
     * @return SignupPayload
    */
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
