import type { CartResponse, CartContainer } from '@/ts/features/cart/types'

export function updateSummaryVisibility(data: CartResponse, cart: CartContainer): void {
  const summaryElement = cart.summary ?? cart.container.querySelector<HTMLElement>('.cart__summary')

  if (!summaryElement) return

  const hasItems = (data.totalItems ?? 0) > 0
  summaryElement.classList.toggle('visible', hasItems)
}
