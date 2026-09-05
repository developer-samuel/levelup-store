<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Handler\Command;

use App\Core\Domain\Segment\Order\Payload\OrderCreatePayload;

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Security\Policy\SecurityPolicyContract,
    Security\Provider\SecurityProviderContract,
    Segment\Cart\Service\Query\CartRenderQueryContract,
    Segment\Order\Handler\Command\CreateOrderHandlerContract,
    Segment\Order\Service\Command\OrderMutationCommandContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

class CreateOrderHandler extends AbstractCommandHandler implements CreateOrderHandlerContract
{
    /**
     * @param SecurityPolicyContract $securityPolicy
     * @param OrderMutationCommandContract $orderMutationCommand
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly SecurityPolicyContract $securityPolicy,
        private readonly SecurityProviderContract $securityProvider,
        private readonly CartRenderQueryContract $cartRenderQuery,
        private readonly OrderMutationCommandContract $orderMutationCommand,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param OrderCreatePayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(OrderCreatePayload $payload): array
    {
        $result = $this->execute(function () use ($payload) {
            $this->securityPolicy->checkIfEmailVerified();

            $result = $this->orderMutationCommand->createOrder($payload);

            if ($result->order !== null) {
                return ApiResultFormatter::success('Order created successfully', null, 'success');
            }

            return ApiResultFormatter::success('Payment redirect successful', null, $result->paymentUrl);
        });

        return $this->withCartOnConflict($result);
    }

    /**
     * @param array<string, mixed> $result
     *
     * @return array<string, mixed>
    */
    private function withCartOnConflict(array $result): array
    {
        if (($result['code'] ?? null) !== 409) {
            return $result;
        }

        $user = $this->securityProvider->getCurrentUser();
        if ($user !== null) {
            $result['cart'] = $this->cartRenderQuery->buildCartResponse($user, '');
        }

        return $result;
    }
}
