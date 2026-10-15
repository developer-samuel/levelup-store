import type { CartInstance } from '@/ts/features/cart/types'
import { toggleCart } from '@/ts/features/cart/_ui/toggle'
import { handleDocumentClick } from '@/ts/features/cart/_handlers/documentClickHandler'

export function attachCartToggleListeners(cart: CartInstance): void {
  const { elements } = cart
  if (!elements) return

  elements.openButton?.addEventListener('click', () => toggleCart(cart, true))
  elements.closeButton?.addEventListener('click', () => toggleCart(cart, false))
}

export function attachCartDocumentClickListener(cart: CartInstance): void {
  document.addEventListener('click', (e: MouseEvent) => void handleDocumentClick(e, cart))
}
