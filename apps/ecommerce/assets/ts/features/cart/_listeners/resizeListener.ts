import { BREAKPOINT_LG } from '@/ts/shared/constants/breakpoints'
import debounce from '@/ts/shared/utils/debounce'

import type { CartInstance } from '@/ts/features/cart/types'
import { toggleCart } from '@/ts/features/cart/_ui/toggle'

export function attachCartResizeListener(cart: CartInstance): void {
  window.addEventListener(
    'resize',
    debounce((): void => {
      if (window.innerWidth >= BREAKPOINT_LG && cart.isOpen) toggleCart(cart, false)
    }, 200),
  )
}
