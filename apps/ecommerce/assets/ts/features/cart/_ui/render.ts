import type { CartResponse } from '@/ts/features/cart/types'
import { getCartElements } from '@/ts/features/cart/_utils/elements'
import { updateTotalPrice } from '@/ts/features/cart/_ui/price'
import { updateSummaryVisibility } from '@/ts/features/cart/_ui/summary'

export function renderCart(data: CartResponse): void {
  const { carts, itemCountHeader } = getCartElements()

  if (itemCountHeader) {
    const totalItems = data.totalItems ?? 0
    itemCountHeader.textContent = totalItems >= 10 ? '9+' : String(totalItems)
  }

  carts.forEach((cart) => {
    if (data.html && cart.contentWrapper) {
      cart.contentWrapper.innerHTML = data.html

      cart.summary = cart.container.querySelector<HTMLElement>('.cart__summary')
      cart.totalPrice = cart.container.querySelector<HTMLElement>('.cart__summary-price-span')
    }

    if (cart.itemCountDetails) {
      cart.itemCountDetails.textContent = `Items: ${data.totalItems ?? 0}`
    }

    updateTotalPrice(cart, data)
    updateSummaryVisibility(data, cart)
  })
}
