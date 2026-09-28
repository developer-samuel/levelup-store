import { attachDestroyListener } from '@/ts/shared/elements/actions/_listeners/destroyListener'

import { handleDeleteClick } from '@/ts/features/wishlist/destroy/_handlers/wishlistDestroyHandler'

export default class WishlistDestroy {
  constructor() {
    attachDestroyListener('.product-item__destroy', handleDeleteClick)
  }
}
