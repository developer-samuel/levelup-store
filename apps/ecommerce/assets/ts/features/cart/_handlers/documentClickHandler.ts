import type { CartInstance } from '@/ts/features/cart/types'
import { toggleCart } from '@/ts/features/cart/_ui/toggle'
import { handleBuy } from '@/ts/features/cart/_handlers/buyHandler'
import { handleRemove } from '@/ts/features/cart/_handlers/removeCartHandler'

function handleOutsideClick(event: MouseEvent, cart: CartInstance, loadingElement: HTMLElement | null): void {
  if (!cart.elements) return
  const { openButton, sidebar } = cart.elements
  if (!sidebar || !openButton) return

  const lastShown = Number(loadingElement?.dataset.lastShown ?? 0)
  const timeSinceLoading = Date.now() - lastShown
  if (timeSinceLoading < 200) return

  const target = event.target instanceof Node ? event.target : null

  if (!sidebar.contains(target) && !openButton.contains(target) && !loadingElement?.contains(target)) {
    toggleCart(cart, false)
  }
}

export async function handleDocumentClick(event: MouseEvent, cart: CartInstance): Promise<void> {
  if (handleBuy(event, cart)) return
  if (await handleRemove(event)) return

  const loadingElement = document.querySelector<HTMLElement>('.loading')

  handleOutsideClick(event, cart, loadingElement)
}
