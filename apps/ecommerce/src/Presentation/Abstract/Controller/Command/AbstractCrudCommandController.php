<?php

declare(strict_types=1);

namespace App\Presentation\Abstract\Controller\Command;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Ports\Shared\Logging\AppLoggerContract;

use App\Presentation\{
    Abstract\Request\AbstractRequest,
    Shared\Responder\HttpResponder,
    Shared\Responder\ResultResponder,
    Shared\Processor\RequestProcessor
};

abstract class AbstractCrudCommandController extends AbstractCommandController
{
    protected ?ValidatorInterface $validator;

    /**
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
     * @param ValidatorInterface|null $validator
    */
    public function __construct(
        protected readonly CsrfTokenManagerInterface $csrfTokenManager,
        AppLoggerContract $logger,
        ?ValidatorInterface $validator = null,
    ) {
        parent::__construct($logger);

        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param string $requestClass
     * @param callable $handler
     * @param bool $redirect
     *
     * @return JsonResponse
    */
    protected function executeCommand(
        Request $request,
        string $requestClass,
        callable $handler,
        bool $redirect = false,
    ): JsonResponse {
        return $this->handleCommand(function () use ($request, $requestClass, $handler, $redirect) {
            /** @var AbstractRequest $crudRequest */
            $crudRequest = $requestClass::fromHttpRequest($request, $this->csrfTokenManager);

            $errors = RequestProcessor::process($crudRequest, $this->validator);
            if ($errors !== null) {
                return $errors;
            }

            $result = $this->executeHandler($handler, $crudRequest);

            return $redirect
                ? ResultResponder::successWithRedirect($result)
                : ResultResponder::success($result);
        });
    }

    /**
     * @param Request $request
     * @param callable(int): array<string, mixed> $handler
     *
     * @return JsonResponse
    */
    protected function executeDeleteCommand(Request $request, callable $handler): JsonResponse
    {
        return $this->handleCommand(function () use ($request, $handler) {
            $id = $request->request->getInt('id');

            /** @var array<string, mixed> $result */
            $result = (array) $handler($id);

            return HttpResponder::success($result);
        });
    }

    /**
     * @param callable $handler
     * @param AbstractRequest $crudRequest
     *
     * @return array<string, mixed>
    */
    private function executeHandler(callable $handler, AbstractRequest $crudRequest): array
    {
        /** @var array<string, mixed> $result */
        $result = (array) $handler($crudRequest);

        return $result;
    }
}
