<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Handler\Command;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Segment\Cart\Service\Command\CartControlCommandContract,
    Segment\Cart\Service\Query\CartControlQueryContract,
    Segment\Order\Handler\Command\OrderSuccessCleanupCommandHandlerContract,
    Segment\Order\Service\Command\OrderPaymentCommandContract,
    Shared\Logging\AppLoggerContract,
    Shared\Persistence\EntityPersistenceContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

final class OrderSuccessCleanupCommandHandler extends AbstractCommandHandler implements OrderSuccessCleanupCommandHandlerContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param CartControlQueryContract $cartControlQuery
     * @param CartControlCommandContract $cartControlCommand
     * @param OrderPaymentCommandContract $orderPaymentCommand
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly EntityPersistenceContract $entityPersistence,
        private readonly CartControlQueryContract $cartControlQuery,
        private readonly CartControlCommandContract $cartControlCommand,
        private readonly OrderPaymentCommandContract $orderPaymentCommand,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param string|null $sessionId
     * @param User $user
     *
     * @return array<string, mixed>
    */
    public function handle(?string $sessionId, User $user): array
    {
        return $this->execute(function () use ($sessionId, $user) {
            $this->handlePaymentSuccess($sessionId);

            $cart = $this->cartControlQuery->getUserCart($user);
            if ($cart !== null) {
                $this->cartControlCommand->clearCart($cart);
                $this->entityPersistence->flush();
            }

            return ApiResultFormatter::success(
                'Order cleaned successfully',
            );
        });
    }

    /**
     * @param string|null $sessionId
     *
     * @return void
    */
    private function handlePaymentSuccess(?string $sessionId): void
    {
        if ($sessionId === null) {
            return;
        }

        $this->orderPaymentCommand->processSuccess($sessionId);
    }
}
