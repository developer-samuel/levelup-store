import { parseQueryParams } from '@/ts/shared/utils/query'

import type { ProductListInstance } from '@/ts/features/products/list/types'
import { bindFilter } from '@/ts/features/products/list/_interactions/filter'

/**
 * Sets up the subtype click filter.
 *
 * Toggles the clicked subtype in/out of the active list and updates the URL param.
 * Applies/removes the active CSS class on the clicked element.
 */
export function setupSubtypeFilter(ctx: ProductListInstance): void {
  bindFilter(
    ctx,
    '[data-subtype]',
    (item: HTMLElement) => {
      const rawSubtype = item.dataset.subtype ?? ''
      const subtype = rawSubtype.trim().toLowerCase().replace(/\s+/g, '-')

      const currentParams = parseQueryParams()
      const subtypesArray = currentParams['subtype'] ? [...currentParams['subtype'].split(',')] : []

      const index = subtypesArray.indexOf(subtype)

      if (index === -1) {
        subtypesArray.push(subtype)
        item.classList.add('products__filter-list-item--active')
      } else {
        subtypesArray.splice(index, 1)
        item.classList.remove('products__filter-list-item--active')
      }

      return { subtype: subtypesArray.join(',') }
    },
    { eventType: 'click' },
  )
}
