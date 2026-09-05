<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Handler\Query;

use App\Core\Domain\{
    Shared\Exception\AccessDeniedException,
    Segment\Cart\Entity\Cart,
    Segment\Cart\Entity\CartItem,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\ValueObject\OrderCreateObject,
    Segment\Product\Specification\ProductVariantAvailabilitySpecification,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Cart\Service\Command\CartControlCommandContract,
    Segment\Cart\Service\Query\CartControlQueryContract,
    Segment\Country\Service\CountryCacheQueryContract,
    Segment\Order\Handler\Query\GetOrderCreateQueryHandlerContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class GetOrderCreateQueryHandler implements GetOrderCreateQueryHandlerContract
{
    /**
     * @param SecurityProviderContract $securityProvider
     * @param CountryCacheQueryContract $countryCacheQuery
     * @param CartControlQueryContract $cartControlQuery
     * @param CartControlCommandContract $cartControlCommand
     * @param EntityPersistenceContract $entityPersistence
    */
    public function __construct(
        private SecurityProviderContract $securityProvider,
        private CountryCacheQueryContract $countryCacheQuery,
        private CartControlQueryContract $cartControlQuery,
        private CartControlCommandContract $cartControlCommand,
        private EntityPersistenceContract $entityPersistence,
    ) {}

    /**
     * @return OrderCreateObject
     *
     * @throws AccessDeniedException
    */
    public function handle(): OrderCreateObject
    {
        $user = $this->securityProvider->getCurrentUser();
        if ($user === null) {
            throw new AccessDeniedException();
        }

        $cart = $this->getUserCart($user);
        $allItems = $this->getCartItems($cart);

        $outOfStockItems = $this->filterOutOfStock($allItems);
        if (!empty($outOfStockItems)) {
            $this->removeItems($outOfStockItems);
        }

        $availableItems = $this->filterAvailable($allItems);

        return new OrderCreateObject(
            personal: $user,
            countries: $this->countryCacheQuery->getAllCountries(),
            paymentMethods: OrderPaymentMethod::cases(),
            cartEmpty: empty($availableItems),
            useShipping: $user->getUseShipping(),
            billing: $user->getBilling(),
            shipping: $user->getShipping(),
        );
    }

    /**
     * @param User $user
     *
     * @return Cart|null
    */
    private function getUserCart(User $user): ?Cart
    {
        return $this->cartControlQuery->getUserCart($user);
    }

    /**
     * @param Cart|null $cart
     *
     * @return CartItem[]
    */
    private function getCartItems(?Cart $cart): array
    {
        if ($cart === null) {
            return [];
        }

        return $cart->getItems()->toArray();
    }

    /**
     * @param CartItem[] $items
     *
     * @return CartItem[]
    */
    private function filterOutOfStock(array $items): array
    {
        return array_values(array_filter(
            $items,
            static fn(CartItem $item): bool =>
                ProductVariantAvailabilitySpecification::findOneInStock($item->getVariant()) === null,
        ));
    }

    /**
     * @param CartItem[] $items
     *
     * @return CartItem[]
    */
    private function filterAvailable(array $items): array
    {
        return array_values(array_filter(
            $items,
            static fn(CartItem $item): bool =>
                ProductVariantAvailabilitySpecification::findOneInStock($item->getVariant()) !== null,
        ));
    }

    /**
     * @param CartItem[] $items
     *
     * @return void
    */
    private function removeItems(array $items): void
    {
        $cart = null;

        foreach ($items as $item) {
            $cart = $cart ?? $item->getCart();
            $this->entityPersistence->remove($item);
        }

        $this->entityPersistence->flush();

        if ($cart !== null) {
            $this->cartControlCommand->flushAndRefreshCart($cart);
        }
    }
}
