import type { ProductListInstance } from '@/ts/features/products/list/types'
import { handleLoadMoreClick } from '@/ts/features/products/list/_handlers/loadMoreHandler'

export function attachLoadMoreListener(ctx: ProductListInstance): void {
  if (!ctx.productsWrapper) return

  ctx.productsWrapper.addEventListener('click', (e: MouseEvent) => {
    handleLoadMoreClick(e, ctx)
  })
}
