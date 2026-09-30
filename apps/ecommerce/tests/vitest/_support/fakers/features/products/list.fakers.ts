import type { StringRecord } from '@/ts/shared/types'

import type { ProductListInstance } from '@/ts/features/products/list/types'

export function makeProductListWrapper(dataset: StringRecord = {}): HTMLElement {
  const el = document.createElement('div')
  Object.entries(dataset).forEach(([k, v]) => (el.dataset[k] = v))
  return el
}

export function makeProductListCtx(overrides: Partial<ProductListInstance> = {}): ProductListInstance {
  return {
    page: 1,
    maxPages: 5,
    isLoading: false,
    ...overrides,
  } as unknown as ProductListInstance
}
