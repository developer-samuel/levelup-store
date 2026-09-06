import debounce from '@/ts/shared/utils/debounce'
import { dispatchLoadingShow, dispatchLoadingHide } from '@/ts/shared/events/loading'

import { isAuth } from '@/ts/core/jwt/isAuth'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import type { CartInstance } from '@/ts/features/cart/types'
import { toggleCart } from '@/ts/features/cart/_ui/toggle'
import { handleCartAction } from '@/ts/features/cart/_handlers/cartActionHandler'

async function performBuy(element: HTMLElement, cart: CartInstance): Promise<void> {
  dispatchLoadingShow()

  try {
    if (!isAuth()) {
      toggleCart(cart, true)

      const warningBox = cart.elements?.warningBox ?? null
      if (warningBox) warningBox.style.display = 'block'

      return
    }

    await handleCartAction(element, 'add')

    toggleCart(cart, true)
  } catch {
    NotyfAlert.error('Something went wrong. Please try again.')
  } finally {
    dispatchLoadingHide()
  }
}

const debouncedPerformBuy = debounce(performBuy, 200)

export function handleBuy(event: MouseEvent, cart: CartInstance): boolean {
  const buyButton = event.target instanceof Element ? event.target.closest<HTMLElement>('.buy-btn') : null
  if (!buyButton) return false

  event.preventDefault()

  debouncedPerformBuy(buyButton, cart)

  return true
}
