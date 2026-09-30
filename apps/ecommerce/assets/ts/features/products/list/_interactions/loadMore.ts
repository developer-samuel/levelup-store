import { dispatchLoadingShow, dispatchLoadingHide } from '@/ts/shared/events/loading'
import { parseQueryParams } from '@/ts/shared/utils/query'

import type { ProductListInstance } from '@/ts/features/products/list/types'
import { updateFromServer } from '@/ts/features/products/list/_utils/pagination'
import { checkLoadMoreVisibility } from '@/ts/features/products/list/_ui/loadMore'
import { updateProducts } from '@/ts/features/products/list/_interactions/updateProducts'

async function loadNextPage(filter: ProductListInstance): Promise<void> {
  dispatchLoadingShow()

  try {
    const params = parseQueryParams()
    const nextPage = Math.max(1, parseInt(filter.productsWrapper.dataset.currentPage ?? String(filter.page), 10)) + 1

    const result = await updateProducts({ ...params, page: nextPage }, filter)

    const serverPage = Number.isFinite(Number(result.currentPage)) ? Number(result.currentPage) : null
    const serverMax = Number.isFinite(Number(result.maxPages)) ? Number(result.maxPages) : null

    const { page, maxPages } = updateFromServer(filter.productsWrapper, serverPage, serverMax)

    filter.page = page
    filter.maxPages = maxPages

    checkLoadMoreVisibility(page, maxPages, filter.productsWrapper)
  } finally {
    dispatchLoadingHide()
  }
}

export async function loadMoreProducts(ctx: ProductListInstance): Promise<void> {
  if (ctx.isLoading) return

  ctx.isLoading = true

  try {
    await loadNextPage(ctx)
  } finally {
    ctx.isLoading = false
  }
}
