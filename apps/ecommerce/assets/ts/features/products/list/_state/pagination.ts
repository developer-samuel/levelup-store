import type { ProductListInstance } from '@/ts/features/products/list/types'

/**
 * Syncs the filter instance's pagination state from the server-returned wrapper.
 *
 * Falls back to the existing dataset / instance values when the server
 * omits pagination metadata.
 */
export function updatePaginationState(
  returnedWrapper: HTMLElement,
  requestedPage: number | undefined,
  filterInstance: ProductListInstance,
): void {
  const returnedCurrent = returnedWrapper.dataset.currentPage ?? null
  const returnedTotal = returnedWrapper.dataset.totalPage ?? null

  filterInstance.productsWrapper.dataset.currentPage =
    returnedCurrent ?? (requestedPage && requestedPage > 0 ? String(requestedPage) : String(filterInstance.page))

  filterInstance.productsWrapper.dataset.totalPage = returnedTotal ?? String(filterInstance.maxPages)

  const dsPage = parseInt(filterInstance.productsWrapper.dataset.currentPage, 10)
  const dsTotal = parseInt(filterInstance.productsWrapper.dataset.totalPage, 10)

  filterInstance.page = Number.isFinite(dsPage) && dsPage > 0 ? dsPage : 1
  filterInstance.maxPages = Number.isFinite(dsTotal) && dsTotal > 0 ? dsTotal : 1
}
