import { handleDestroy } from '@/ts/shared/elements/actions/_handlers/destroyHandler'

import { updateWishlistWrapper } from '@/ts/features/wishlist/destroy/_ui/wrapper'
import { wishlistDestroy } from '@/ts/features/wishlist/destroy/_services/destroyService'

export const handleDeleteClick = handleDestroy({
  datasetKey: 'variantId',
  serviceFn: wishlistDestroy,
  onSuccess: (el) => updateWishlistWrapper(el.closest('.product-item')),
  logTag: '[Wishlist]',
  successMessage: 'Item removed from wishlist.',
})
