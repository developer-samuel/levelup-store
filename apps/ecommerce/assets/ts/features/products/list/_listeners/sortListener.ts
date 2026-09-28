import type { ProductListInstance } from '@/ts/features/products/list/types'
import { handleSort } from '@/ts/features/products/list/_handlers/sortHandler'

export function attachSortListener(ctx: ProductListInstance, sortSelect: HTMLElement | null): void {
  if (!sortSelect) return

  sortSelect.addEventListener('change', (e: Event) => void handleSort(e, ctx))
}
