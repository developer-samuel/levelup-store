import type { ProductListInstance } from '@/ts/features/products/list/types'
import { bindFilter } from '@/ts/features/products/list/_interactions/filter'

/**
 * Sets up the min/max price range filter.
 *
 * Fires on 'change' (after the user releases the slider) and reads both
 * price inputs regardless of which one changed.
 */
export function setupPriceFilter(ctx: ProductListInstance): void {
  bindFilter(
    ctx,
    '#minPrice, #maxPrice',
    () => ({
      minPrice: (document.getElementById('minPrice') as HTMLInputElement | null)?.value ?? '',
      maxPrice: (document.getElementById('maxPrice') as HTMLInputElement | null)?.value ?? '',
    }),
    { eventType: 'change' },
  )
}
