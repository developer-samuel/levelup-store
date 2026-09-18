<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Cart\Service\Command;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Segment\Cart\Repository\CartItemRepositoryContract,
    Segment\Cart\Service\Command\CartControlCommandContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class CartControlCommandService implements CartControlCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param CartItemRepositoryContract $cartItemRepository
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private CartItemRepositoryContract $cartItemRepository,
    ) {}

    /**
     * @param Cart $cart
     *
     * @return void
    */
    public function clearCart(Cart $cart): void
    {
        $this->clearItemsOrCart($cart);

        $this->entityPersistence->remove($cart, true);
    }

    /**
     * @param Cart $cart
     *
     * @return void
    */
    public function flushAndRefreshCart(Cart $cart): void
    {
        $cart->setUpdatedAt();

        $this->entityPersistence->flush();

        $this->entityPersistence->refresh($cart);
        $this->clearItemsOrCart($cart);

        $this->entityPersistence->flush();
    }

    /**
     * @param User $user
     *
     * @return Cart
    */
    public function createNewCart(User $user): Cart
    {
        $cart = new Cart($user);

        $this->entityPersistence->persist($cart, true);

        return $cart;
    }

    /**
     * @param Cart $cart
     *
     * @return void
    */
    private function clearItemsOrCart(Cart $cart): void
    {
        $remainingItems = $this->cartItemRepository->findByCart($cart);
        if ($remainingItems === []) {
            $this->entityPersistence->remove($cart, true);
        }
    }
}
