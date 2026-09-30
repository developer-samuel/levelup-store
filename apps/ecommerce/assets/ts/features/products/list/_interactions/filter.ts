import { queryAll } from '@/ts/shared/utils/dom/query'

import type { ProductListInstance, ValueCallback } from '@/ts/features/products/list/types'
import { handleFilter } from '@/ts/features/products/list/_handlers/filterHandler'

/** Queries all elements matching 'selector' and binds each one to a filter handler */
export function bindFilter(
  ctx: ProductListInstance,
  selector: string,
  valueCallback: ValueCallback,
  options: { eventType?: string } = {},
): void {
  const { eventType = 'change' } = options

  const elements = queryAll<HTMLElement>(selector)
  if (!elements.length) return

  elements.forEach((el) => {
    el.addEventListener(eventType, () => void handleFilter(el, ctx, valueCallback))
  })
}
