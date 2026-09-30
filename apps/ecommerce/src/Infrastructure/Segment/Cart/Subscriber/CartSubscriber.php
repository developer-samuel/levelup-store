<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Cart\Subscriber;

use Symfony\{
    Bundle\SecurityBundle\Security,
    Component\EventDispatcher\EventSubscriberInterface,
    Component\HttpKernel\Event\ControllerEvent
};

use Twig\Environment;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\{
    Segment\Cart\Service\Query\CartSummaryQueryContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\PriceFormatter;

final readonly class CartSubscriber implements EventSubscriberInterface
{
    /**
     * @param Security $security
     * @param Environment $twig
     * @param CartSummaryQueryContract $cartSummaryQuery
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private Security $security,
        private Environment $twig,
        private CartSummaryQueryContract $cartSummaryQuery,
        private AppLoggerContract $logger,
    ) {}

    /**
     * @return array<string, string>
    */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onKernelController',
        ];
    }

    /**
     * @param ControllerEvent $event
     *
     * @return void
    */
    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            $this->addGlobalVariablesToTwig(new User(), [], 0.0, 0);

            return;
        }

        $summary = $this->getCartSummarySafe($user);

        $this->addGlobalVariablesToTwig(
            $user,
            $summary['items'],
            $summary['totalPrice'],
            $summary['totalItems'],
        );
    }

    /**
     * @param User $user
     *
     * @return array{
     *     items: array<mixed>,
     *     totalPrice: float,
     *     totalItems: int
     * }
    */
    private function getCartSummarySafe(User $user): array
    {
        try {
            return $this->cartSummaryQuery->getCartSummary($user->getId());
        } catch (\Throwable $throwable) {
            $this->logger->alert(
                'CartSubscriber error while fetching cart summary',
                $throwable,
                $user,
            );

            return [
                'items'      => [],
                'totalPrice' => 0.0,
                'totalItems' => 0,
            ];
        }
    }

    /**
     * @param User $user
     * @param array<mixed> $items
     * @param float $totalPrice
     * @param int $totalItems
     *
     * @return void
    */
    private function addGlobalVariablesToTwig(
        User $user,
        array $items,
        float $totalPrice,
        int $totalItems,
    ): void {
        $this->twig->addGlobal('user', $user);
        $this->twig->addGlobal('cart', $items);
        $this->twig->addGlobal('totalPrice', PriceFormatter::format($totalPrice));
        $this->twig->addGlobal('totalItems', $totalItems);
    }
}
