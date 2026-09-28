import type { ProductListInstance } from '@/ts/features/products/list/types'
import { loadMoreProducts } from '@/ts/features/products/list/_interactions/loadMore'

/** Handles delegated click on productsWrapper - targets #load-more button */
export function handleLoadMoreClick(e: MouseEvent, ctx: ProductListInstance): void {
  const btn = e.target instanceof Element ? e.target.closest('#load-more') : null
  if (!btn) return
  if (ctx.isLoading) return

  e.preventDefault()
  void loadMoreProducts(ctx)
}
