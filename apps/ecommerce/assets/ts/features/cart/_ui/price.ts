import type { CartResponse, CartContainer } from '@/ts/features/cart/types'

export function updateTotalPrice(cart: CartContainer, data: CartResponse): void {
  if (!cart.totalPrice) return

  const raw = data.totalPrice
  const price = typeof raw === 'number' ? raw : parseFloat(String(raw ?? '0')) || 0

  cart.totalPrice.textContent = Number.isInteger(price) ? `${price} €` : `${price.toFixed(2)} €`
}
