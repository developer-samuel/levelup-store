<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Order\Controller\Command;

use Symfony\{
    Component\HttpFoundation\Response,
    Component\HttpFoundation\Request,
    Component\Routing\Generator\UrlGeneratorInterface,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Domain\{
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\Address\OrderShippingObject,
    Segment\Order\ValueObject\OrderPersonalObject
};

use App\Core\Ports\{
    Segment\Order\Handler\Command\CreateOrderHandlerContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCommandController,
    Shared\Responder\ResultResponder,
    Segment\Order\Request\OrderRequest,
    Shared\Processor\RequestProcessor
};

final class OrderCommandController extends AbstractCommandController
{
    /**
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param ValidatorInterface $validator
     * @param UrlGeneratorInterface $urlGenerator
     * @param CreateOrderHandlerContract $createOrderHandler
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly ValidatorInterface $validator,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly CreateOrderHandlerContract $createOrderHandler,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param Request $request
     *
     * @return Response
    */
    public function store(Request $request): Response {
        return $this->handleCommand(function () use ($request) {
            $orderRequest = OrderRequest::fromHttpRequest(
                $request,
                $this->csrfTokenManager,
            );

            $errors = RequestProcessor::process($orderRequest, $this->validator);
            if ($errors !== null) {
                return $errors;
            }

            $payload = $this->createPayload($orderRequest);

            $result = $this->handleRedirect(
                $this->createOrderHandler->handle($payload),
            );

            return ResultResponder::successWithRedirect($result);
        });
    }

    /**
     * @param OrderRequest $request
     *
     * @return OrderCreatePayload
     *
     * @throws \InvalidArgumentException
    */
    private function createPayload(OrderRequest $request): OrderCreatePayload
    {
        $sendShipping = $request->send_shipping;
        $paymentMethod = $this->validateAndMapPaymentMethod($request->payment_method);

        return new OrderCreatePayload(
            personal: $this->createPersonalObject($request),
            sendShipping: $sendShipping,
            paymentMethod: $paymentMethod,
            billing: $this->createBillingObject($request),
            shipping: $sendShipping ? $this->createShippingObject($request) : null,
        );
    }

    /**
     * @param OrderRequest $request
     *
     * @return OrderPersonalObject
    */
    private function createPersonalObject(OrderRequest $request): OrderPersonalObject
    {
        return new OrderPersonalObject(
            email: $request->email,
            firstName: $request->first_name,
            lastName: $request->last_name,
        );
    }

    /**
     * @param OrderRequest $request
     *
     * @return OrderBillingObject
    */
    private function createBillingObject(OrderRequest $request): OrderBillingObject
    {
        return new OrderBillingObject(
            country: $request->billing_country,
            street: $request->billing_street,
            postalCode: $request->billing_postal_code,
            city: $request->billing_city,
        );
    }

    /**
     * @param OrderRequest $request
     *
     * @return OrderShippingObject
    */
    private function createShippingObject(OrderRequest $request): OrderShippingObject
    {
        return new OrderShippingObject(
            country: $request->shipping_country,
            street: $request->shipping_street,
            postalCode: $request->shipping_postal_code,
            city: $request->shipping_city,
        );
    }

    /**
     * @param string $paymentMethod
     *
     * @return OrderPaymentMethod
     *
     * @throws \InvalidArgumentException
    */
    private function validateAndMapPaymentMethod(string $paymentMethod): OrderPaymentMethod
    {
        $method = OrderPaymentMethod::tryFrom($paymentMethod);
        if ($method === null) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid payment method "%s". Allowed values: %s',
                $paymentMethod,
                implode(', ', OrderPaymentMethod::values()),
            ));
        }

        return $method;
    }

    /**
     * @param array<string, mixed> $result
     *
     * @return array<string, mixed>
    */
    private function handleRedirect(array $result): array
    {
        if (isset($result['redirect_route']) && is_string($result['redirect_route'])) {
            $result['redirect'] = $this->urlGenerator->generate($result['redirect_route']);
            unset($result['redirect_route']);
        }

        return $result;
    }
}
