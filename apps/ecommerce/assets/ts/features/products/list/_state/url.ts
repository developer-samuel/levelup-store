import { buildQueryString } from '@/ts/shared/utils/query'

import type { ProductListParams } from '@/ts/features/products/list/types'

/**
 * Updates the browser URL to reflect the given filter parameters
 * without reloading the page.
 */
export function updateUrlState(params: ProductListParams): string {
  const queryString = buildQueryString(params)
  const url = window.location.pathname + (queryString ? `?${queryString}` : '')

  history.pushState(null, '', url)

  return url
}
