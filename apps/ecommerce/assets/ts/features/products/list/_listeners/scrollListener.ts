import { HEADER_TOGGLE } from '@/ts/presentation/layout/header/_events/toggle'
import { handleStickyOffsetToggle } from '@/ts/features/products/list/_handlers/scrollHandler'

export function attachScrollListener(): void {
  const filterMobile = document.querySelector<HTMLElement>('.products__filter-mobile')
  const cardOptions = document.querySelector<HTMLElement>('.products__card-options')

  if (!filterMobile && !cardOptions) return

  document.addEventListener(HEADER_TOGGLE, (e) => handleStickyOffsetToggle(e, filterMobile, cardOptions))
}
