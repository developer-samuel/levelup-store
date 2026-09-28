import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import type { CartAction, CartResponse } from '@/ts/features/cart/types'
import { getCartErrorMessage } from '@/ts/features/cart/_utils/cartResponse'
import { renderCart } from '@/ts/features/cart/_ui/render'
import { cartAdd, cartRemove } from '@/ts/features/cart/_services/cartService'

export async function handleCartAction(element: HTMLElement, action: CartAction): Promise<void> {
  const id = element.dataset.variantId ?? element.closest<HTMLElement>('.cart__content-item')?.dataset.itemId

  if (!id) return

  try {
    let data: CartResponse | null = null

    if (action === 'add') data = await cartAdd(id)
    else if (action === 'remove') data = await cartRemove(id)

    if (!data) return

    const { success = false, message = '' } = data

    if (message) {
      if (success) NotyfAlert.success(message)
      else NotyfAlert.error(message)
    }

    renderCart(data)
  } catch (error) {
    NotyfAlert.error(getCartErrorMessage(error))
  }
}
