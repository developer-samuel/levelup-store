import { parseQueryParams } from '@/ts/shared/utils/query'

import type { ProductListInstance } from '@/ts/features/products/list/types'
import { resetFilterUI } from '@/ts/features/products/list/_ui/reset'
import { setupBrandFilter } from '@/ts/features/products/list/_filters/brandFilter'
import { setupPriceFilter } from '@/ts/features/products/list/_filters/priceFilter'
import { setupSubtypeFilter } from '@/ts/features/products/list/_filters/subtypeFilter'
import { attachLoadMoreListener } from '@/ts/features/products/list/_listeners/loadMoreListener'
import { attachSortListener } from '@/ts/features/products/list/_listeners/sortListener'
import { updateProducts } from '@/ts/features/products/list/_interactions/updateProducts'

export function attachFilterListener(ctx: ProductListInstance): void {
  setupSubtypeFilter(ctx)
  setupBrandFilter(ctx)
  setupPriceFilter(ctx)

  const sortSelect = document.getElementById('sort-by')
  attachSortListener(ctx, sortSelect)

  attachLoadMoreListener(ctx)

  const maxPriceEl = document.getElementById('maxPrice') as HTMLInputElement | null
  const initialMaxPrice = maxPriceEl?.max ?? maxPriceEl?.value ?? ''

  const resetBtn = document.getElementById('filter-reset')
  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      const params = parseQueryParams()
      const { category, type, sort, page } = params

      const NON_FILTER_KEYS = new Set(['category', 'type', 'sort', 'page'])
      const hasFilterParams = Object.keys(params).some((key) => !NON_FILTER_KEYS.has(key))
      const hasPageBeyondFirst = page !== undefined && page !== '1'

      if (!hasFilterParams && !hasPageBeyondFirst) return

      resetFilterUI(initialMaxPrice)
      ctx.page = 1
      void updateProducts({ category, type, ...(sort !== undefined && { sort }), page: 1 }, ctx)
    })
  }
}
