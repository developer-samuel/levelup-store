<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Product\Controller\Command\Variant\Abstract;

use Symfony\{
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Ports\{
    Shared\Encryption\HmacFieldDecoderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Shared\Utils\IdDecoder
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

abstract class AbstractAdminVariantCommandController extends AbstractCrudCommandController
{
    /**
     * @param HmacFieldDecoderContract $hmacFieldDecoder
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
     * @param ValidatorInterface $validator
    */
    public function __construct(
        protected readonly HmacFieldDecoderContract $hmacFieldDecoder,
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
     * @param string $action
     *
     * @return string
    */
    abstract protected function getSuccessMessage(string $action): string;

    /**
     * @template TRequest of object
     * @template TPayload
     *
     * @param TRequest $request
     * @param callable(TRequest): TPayload $createPayloadCallback
     * @param callable(TPayload): array<string, mixed> $handler
     *
     * @return array<string, mixed>
    */
    protected function handleCreateCommand(object $request, callable $createPayloadCallback, callable $handler): array
    {
        $this->prepareVariantId($request);

        $payload = $createPayloadCallback($request);

        $handler($payload);

        return ApiResultFormatter::success($this->getSuccessMessage('created'));
    }

    /**
     * @template TRequest of object
     * @template TPayload
     *
     * @param TRequest $request
     * @param callable(TRequest,string): TPayload $createPayloadCallback
     * @param callable(TPayload): array<string, mixed> $handler
     *
     * @return array<string, mixed>
    */
    protected function handleUpdateCommand(object $request, callable $createPayloadCallback, callable $handler): array
    {
        $id = $this->prepareId($request);
        $this->prepareVariantId($request);

        $payload = $createPayloadCallback($request, $id);

        $handler($payload);

        return ApiResultFormatter::success($this->getSuccessMessage('updated'));
    }

    /**
     * @param object $request
     *
     * @return string
    */
    private function prepareVariantId(object $request): string
    {
        return $this->ensureProperty($request, 'variantId');
    }

    /**
     * @param object $request
     *
     * @return string
    */
    private function prepareId(object $request): string
    {
        return $this->ensureProperty($request, 'id');
    }

    /**
     * @param object $request
     * @param string $propertyName
     *
     * @return string
    */
    private function ensureProperty(object $request, string $propertyName): string
    {
        $decoded = match ($propertyName) {
            'id'        => $this->decodeId($request),
            'variantId' => $this->decodeVariantId($request),
            default     => throw new \LogicException('Unsupported property: ' . $propertyName),
        };

        $decodedString = (string) $decoded;

        /** @var \stdClass $request */
        $request->{$propertyName} = $decodedString;

        return $decodedString;
    }

    /**
     * @param object $request
     *
     * @return int
    */
    private function decodeVariantId(object $request): int
    {
        return IdDecoder::decode(
            $this->hmacFieldDecoder,
            $request,
            'variantId',
        );
    }

    /**
     * @param object $request
     *
     * @return int
    */
    private function decodeId(object $request): int
    {
        return IdDecoder::decode(
            $this->hmacFieldDecoder,
            $request,
            'id',
        );
    }
}
