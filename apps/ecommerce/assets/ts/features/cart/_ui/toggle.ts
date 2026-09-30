import type { CartInstance } from '@/ts/features/cart/types'

export function toggleCart(cart: CartInstance, open: boolean): void {
  cart.isOpen = open
  if (open) cart.openedAt = Date.now()
  cart.elements?.sidebar?.classList.toggle('cart--active', open)

  document.getElementById('cart-backdrop')?.classList.toggle('cart--active', open)
}
