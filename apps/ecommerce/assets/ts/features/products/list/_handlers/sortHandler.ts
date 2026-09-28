import { dispatchLoadingShow, dispatchLoadingHide } from '@/ts/shared/events/loading'
import { parseQueryParams } from '@/ts/shared/utils/query'
import { scrollToTop } from '@/ts/shared/utils/scroll'

import type { ProductListInstance } from '@/ts/features/products/list/types'
import { updateProducts } from '@/ts/features/products/list/_interactions/updateProducts'

/** Handles sort-select 'change' events */
export async function handleSort(e: Event, ctx: ProductListInstance): Promise<void> {
  ctx.page = 1

  scrollToTop()

  const currentParams = parseQueryParams()
  const sortValue = e.target instanceof HTMLSelectElement ? e.target.value : ''

  dispatchLoadingShow()

  try {
    await updateProducts({ ...currentParams, sort: sortValue, page: ctx.page }, ctx)
  } finally {
    dispatchLoadingHide()
  }
}
