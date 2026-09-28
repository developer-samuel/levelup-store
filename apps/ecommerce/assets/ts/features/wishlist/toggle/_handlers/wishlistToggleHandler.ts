import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import { updateWishlist } from '@/ts/features/wishlist/toggle/_ui/icon'
import { wishlistToggle } from '@/ts/features/wishlist/toggle/_services/wishlistService'

let isToggling = false

export async function handleWishlistToggle(event: MouseEvent, el: HTMLElement, variantId: string): Promise<void> {
  event.preventDefault()
  event.stopPropagation()

  if (isToggling) return
  isToggling = true

  const isActive = el.dataset.active === 'true'

  el.dataset.active = isActive ? 'false' : 'true'
  updateWishlist(el)

  try {
    await wishlistToggle(variantId)
  } catch {
    el.dataset.active = isActive ? 'true' : 'false'
    updateWishlist(el)

    NotyfAlert.error('Something went wrong. Please try again.')
  } finally {
    isToggling = false
  }
}
