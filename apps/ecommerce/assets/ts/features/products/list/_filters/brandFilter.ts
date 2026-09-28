import { queryAll } from '@/ts/shared/utils/dom/query'

import type { ProductListInstance } from '@/ts/features/products/list/types'
import { bindFilter } from '@/ts/features/products/list/_interactions/filter'

/**
 * Sets up the brand checkbox filter.
 *
 * Collects all checked brand values, normalises them to slug form,
 * and returns them as a comma-separated 'brand' param.
 */
export function setupBrandFilter(ctx: ProductListInstance): void {
  bindFilter(ctx, 'input[name="brand[]"]', () => {
    const checked = Array.from(queryAll<HTMLInputElement>('input[name="brand[]"]:checked')).map((b) =>
      b.value.trim().toLowerCase().replace(/\s+/g, '-'),
    )

    return { brand: checked.join(',') }
  })
}
