import { dispatchLoadingShow, dispatchLoadingHide } from '@/ts/shared/events/loading'
import { logDevError } from '@/ts/shared/utils/logger'
import { scrollToTop } from '@/ts/shared/utils/scroll'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import type { ProductListInstance, ProductListParams } from '@/ts/features/products/list/types'
import { normalizeLoadMore } from '@/ts/features/products/list/_ui/loadMore'
import { updateProductList } from '@/ts/features/products/list/_ui/updater'
import { parseProductWrapper } from '@/ts/features/products/list/_ui/wrapper'
import { updatePaginationState } from '@/ts/features/products/list/_state/pagination'
import { updateUrlState } from '@/ts/features/products/list/_state/url'
import { fetchProductHtml } from '@/ts/features/products/list/_services/productsService'

type UpdateProductsResult = {
  currentPage?: number
  maxPages?: number
}

/**
 * Fetches filtered/paged products, updates the DOM and pagination state.
 *
 * Scrolls to top when resetting to page 1.
 */
export async function updateProducts(
  params: ProductListParams,
  filterInstance: ProductListInstance,
): Promise<UpdateProductsResult> {
  dispatchLoadingShow()

  const resetPage = params.page === 1

  try {
    const url = updateUrlState(params)
    const html = await fetchProductHtml(url)
    const returnedWrapper = parseProductWrapper(html)

    const newList = returnedWrapper.querySelector<HTMLElement>('.products__list')
    const oldList = filterInstance.productsWrapper.querySelector<HTMLElement>('.products__list')
    const oldLoadMore = filterInstance.productsWrapper.querySelector<HTMLElement>('.products__card-load-more')
    const requestedPage = params.page !== undefined ? Number(params.page) : undefined

    updateProductList(newList, oldList, returnedWrapper, oldLoadMore, requestedPage, filterInstance)
    normalizeLoadMore(filterInstance.productsWrapper)
    updatePaginationState(returnedWrapper, requestedPage, filterInstance)

    return {
      currentPage: filterInstance.page,
      maxPages: filterInstance.maxPages,
    }
  } catch (error) {
    logDevError('[Products]', error)
    NotyfAlert.error('Something went wrong. Please try again.')
    return {}
  } finally {
    dispatchLoadingHide()

    if (resetPage) scrollToTop()
  }
}
