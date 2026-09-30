import { BREAKPOINT_XL } from '@/ts/shared/constants/breakpoints'
import { query } from '@/ts/shared/utils/dom/query'

import { toggle } from '@/ts/features/products/list/_ui/visibility'
import { attachCloseButtonListener } from '@/ts/features/products/list/_listeners/closeButtonListener'
import { attachDocumentClickListener } from '@/ts/features/products/list/_listeners/documentClickListener'
import { attachMobileFilterButtonListener } from '@/ts/features/products/list/_listeners/mobileFilterButtonListener'
import { attachResizeListener } from '@/ts/features/products/list/_listeners/resizeListener'

export function setupMobileFilter(): void {
  const mobileFilterBtn = query<HTMLElement>('.products__filter-mobile-icon')
  const productFilter = query<HTMLElement>('.products__filter')
  const closeBtn = productFilter?.querySelector<HTMLElement>('.products__filter-close') ?? null

  if (productFilter) {
    toggle(productFilter, window.innerWidth >= BREAKPOINT_XL)
  }

  attachResizeListener(productFilter)
  attachMobileFilterButtonListener(mobileFilterBtn, productFilter)
  attachCloseButtonListener(closeBtn, productFilter)
  attachDocumentClickListener(mobileFilterBtn, productFilter)
}
