import type { CartInstance } from '@/ts/features/cart/types'
import { toggleCart } from '@/ts/features/cart/_ui/toggle'

export function attachCartWarningListener(cart: CartInstance): void {
  const { elements } = cart
  if (!elements?.warningCloseButton) return

  elements.warningCloseButton.addEventListener('click', () => {
    toggleCart(cart, false)
    setTimeout(() => {
      if (elements.warningBox) elements.warningBox.style.display = 'none'
    }, 500)
  })
}
