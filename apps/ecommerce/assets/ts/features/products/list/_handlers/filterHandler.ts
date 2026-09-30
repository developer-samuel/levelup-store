import { parseQueryParams } from '@/ts/shared/utils/query'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import type { ProductListInstance, ValueCallback } from '@/ts/features/products/list/types'
import { updateProducts } from '@/ts/features/products/list/_interactions/updateProducts'

/**
 * Creates an async event handler for a filter element.
 *
 * Resets to page 1, merges new filter values into current URL params,
 * and fetches updated products.
 */
export async function handleFilter(
  el: HTMLElement,
  ctx: ProductListInstance,
  valueCallback: ValueCallback,
): Promise<void> {
  ctx.page = 1

  const currentParams = parseQueryParams()
  const filterParams = valueCallback(el) ?? {}

  try {
    await updateProducts({ ...currentParams, ...filterParams, page: ctx.page }, ctx)
  } catch {
    NotyfAlert.error('Something went wrong. Please try again.')
  }
}
