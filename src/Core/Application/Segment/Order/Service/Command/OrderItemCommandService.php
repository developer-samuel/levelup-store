<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Command;

use App\Core\Domain\{
    Shared\Exception\ConflictException,
    Segment\Cart\Entity\CartItem,
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderItem,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\Enum\Variant\ProductVariantEanStatus,
};

use App\Core\Ports\{
    Segment\Cart\Service\Command\CartItemCommandContract,
    Segment\Order\Service\Command\OrderItemCommandContract,
    Segment\Order\Service\Query\OrderItemQueryContract,
    Segment\Product\Repository\Variant\ProductVariantEanRepositoryContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class OrderItemCommandService implements OrderItemCommandContract
{
    private const CART_STOCK_REMOVED_MSG = 'Your cart has been updated. Some products are no longer in stock and have been removed.';

    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param ProductVariantEanRepositoryContract $eanRepository
     * @param OrderItemQueryContract $orderItemQuery
     * @param CartItemCommandContract $cartItemCommand
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private ProductVariantEanRepositoryContract $eanRepository,
        private OrderItemQueryContract $orderItemQuery,
        private CartItemCommandContract $cartItemCommand,
    ) {}

    /**
     * @param Order $order
     * @param CartItem[] $cartItems
     *
     * @return void
    */
    public function processOrderItems(Order $order, array $cartItems): void
    {
        $variantCounts = $this->aggregateVariants($cartItems);

        foreach ($variantCounts as $variantData) {
            $this->createOrderItemsForVariant(
                $order,
                $variantData['variant'],
                $variantData['count'],
                $cartItems,
            );
        }
    }

    /**
     * @param CartItem[] $cartItems
     *
     * @return void
     *
     * @throws ConflictException
    */
    public function validateAllItemsInStock(array $cartItems): void
    {
        $variantCounts = $this->aggregateVariants($cartItems);

        foreach ($variantCounts as $variantData) {
            $variant = $variantData['variant'];
            $quantityInCart = $variantData['count'];

            $stock = $variant->getStock();
            if (!$stock instanceof ProductVariantStock) {
                $this->cartItemCommand->removeVariant($variant, $cartItems);

                throw new ConflictException(
                    self::CART_STOCK_REMOVED_MSG,
                );
            }

            $availableEans = $this->eanRepository->findAvailableByVariant($variant);

            $this->validateStockOrRemove($variant, $cartItems, $stock, $availableEans, $quantityInCart);
        }
    }

    /**
     * @param CartItem[] $cartItems
     *
     * @return array<int, array{
     *     variant: ProductVariant,
     *     count: int
     * }>
    */
    private function aggregateVariants(array $cartItems): array
    {
        $variantCounts = [];

        foreach ($cartItems as $cartItem) {
            $variant = $cartItem->getVariant();
            $variantId = (int) $variant->getId();

            if (!isset($variantCounts[$variantId])) {
                $variantCounts[$variantId] = [
                    'variant' => $variant,
                    'count'   => 1,
                ];
                continue;
            }

            $variantCounts[$variantId]['count']++;
        }

        return $variantCounts;
    }

    /**
     * @param Order $order
     * @param ProductVariant $variant
     * @param int $quantityInCart
     * @param CartItem[] $cartItems
     *
     * @return void
     *
     * @throws ConflictException
    */
    private function createOrderItemsForVariant(
        Order $order,
        ProductVariant $variant,
        int $quantityInCart,
        array $cartItems,
    ): void {
        $stock = $variant->getStock();
        if (!$stock instanceof ProductVariantStock) {
            throw new ConflictException(
                self::CART_STOCK_REMOVED_MSG,
            );
        }

        $availableEans = $this->eanRepository->findAvailableByVariant($variant);

        $this->validateStockOrRemove($variant, $cartItems, $stock, $availableEans, $quantityInCart);

        $this->reserveEansAndCreateItems($order, $variant, $availableEans, $quantityInCart);
        $this->updateStock($stock, $quantityInCart);
    }

    /**
     * @param ProductVariant $variant
     * @param CartItem[] $cartItems
     * @param ProductVariantStock $stock
     * @param ProductVariantEan[] $availableEans
     * @param int $quantityInCart
     *
     * @return void
     *
     * @throws ConflictException
    */
    private function validateStockOrRemove(
        ProductVariant $variant,
        array $cartItems,
        ProductVariantStock $stock,
        array $availableEans,
        int $quantityInCart,
    ): void {
        if (!$this->orderItemQuery->isStockAvailable($stock)
            || empty($availableEans)
            || $quantityInCart > count($availableEans)
        ) {
            $this->cartItemCommand->removeVariant($variant, $cartItems);

            throw new ConflictException(
                self::CART_STOCK_REMOVED_MSG,
            );
        }
    }

    /**
     * @param Order $order
     * @param ProductVariant $variant
     * @param ProductVariantEan[] $availableEans
     * @param int $quantityToReserve
     *
     * @return void
    */
    private function reserveEansAndCreateItems(Order $order, ProductVariant $variant, array $availableEans, int $quantityToReserve): void
    {
        for ($i = 0; $i < $quantityToReserve; $i++) {
            $ean = $availableEans[$i];
            $ean->setStatus(ProductVariantEanStatus::RESERVED);

            $this->entityPersistence->persist($ean);

            $item = new OrderItem();
            $item->setOrder($order)
                ->setVariant($variant)
                ->setPrice($variant->getDiscountedPrice())
                ->setEan($ean);

            $this->entityPersistence->persist($item);
        }
    }

    /**
     * @param ProductVariantStock $stock
     * @param int $quantityToReserve
     *
     * @return void
    */
    private function updateStock(ProductVariantStock $stock, int $quantityToReserve): void
    {
        $stock->reserveQuantity($quantityToReserve);

        $this->entityPersistence->persist($stock);
    }
}
