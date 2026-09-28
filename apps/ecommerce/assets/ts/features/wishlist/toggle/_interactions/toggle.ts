import { queryAll } from '@/ts/shared/utils/dom/query'
import { attachToggleListener } from '@/ts/shared/elements/actions/_listeners/toggleListener'

import { updateWishlist } from '@/ts/features/wishlist/toggle/_ui/icon'
import { handleWishlistToggle } from '@/ts/features/wishlist/toggle/_handlers/wishlistToggleHandler'

export function bindToggle(selector: string): void {
  queryAll<HTMLElement>(selector).forEach((el) => {
    el.addEventListener('mouseenter', () => updateWishlist(el, true))
    el.addEventListener('mouseleave', () => updateWishlist(el, false))
  })

  attachToggleListener(
    selector,
    (el) => ({
      id: el.dataset.variantId,
      email: el.dataset.userEmail,
    }),
    handleWishlistToggle,
    1000,
  )
}
