<?php

declare(strict_types=1);

namespace App\Scheduler\Task\Cart;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Cart\Entity\CartItem,
    Segment\Product\Specification\ProductVariantAvailabilitySpecification
};

use App\Core\Ports\{
    Segment\Cart\Repository\CartItemRepositoryContract,
    Shared\Logging\ConsoleLoggerContract
};

use App\Scheduler\{
    Message\Cart\CartStockCleanupMessage,
    Task\Abstract\AbstractTask
};

#[AsMessageHandler]
class CartStockCleanupTask extends AbstractTask
{
    /**
     * @param CartItemRepositoryContract $cartItemRepository
     * @param EntityManagerInterface $entityManager
     * @param ConsoleLoggerContract $logger
    */
    public function __construct(
        private readonly CartItemRepositoryContract $cartItemRepository,
        EntityManagerInterface $entityManager,
        ConsoleLoggerContract $logger,
    ) {
        parent::__construct($entityManager, $logger);
    }

    /**
     * @param CartStockCleanupMessage $message
     *
     * @return void
    */
    public function __invoke(CartStockCleanupMessage $message): void
    {
        $this->execute();
    }

    /**
     * @return string
    */
    protected function getTaskName(): string
    {
        return 'CartStockCleanupTask';
    }

    /**
     * @return CartItem[]
    */
    protected function fetchItems(): iterable
    {
        return $this->cartItemRepository->findAllWithVariant();
    }

    /**
     * @param iterable<CartItem> $items
     *
     * @return int
    */
    protected function processItems(iterable $items): int
    {
        $count = 0;
        $affectedCarts = [];

        foreach ($items as $item) {
            if (ProductVariantAvailabilitySpecification::findOneInStock($item->getVariant()) !== null) {
                continue;
            }

            $cart = $item->getCart();
            if ($cart === null) {
                continue;
            }

            $affectedCarts[$cart->getId()] = $cart;

            $this->entityManager->remove($item);
            $count++;
        }

        if ($count === 0) {
            return 0;
        }

        $this->entityManager->flush();

        $this->removeEmptyCarts($affectedCarts);

        return $count;
    }

    /**
     * @param Cart[] $carts
     *
     * @return void
    */
    private function removeEmptyCarts(array $carts): void
    {
        foreach ($carts as $cart) {
            if ($cart->getItems()->isEmpty()) {
                $this->entityManager->remove($cart);
            }
        }

        $this->entityManager->flush();
    }
}
