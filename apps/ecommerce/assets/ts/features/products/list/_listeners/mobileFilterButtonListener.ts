import { BREAKPOINT_XL } from '@/ts/shared/constants/breakpoints'

import { show } from '@/ts/features/products/list/_ui/visibility'

export function attachMobileFilterButtonListener(
  mobileFilterBtn: HTMLElement | null,
  productFilter: HTMLElement | null,
): void {
  if (!mobileFilterBtn || !productFilter) return

  mobileFilterBtn.addEventListener('click', () => {
    if (window.innerWidth < BREAKPOINT_XL) show(productFilter)
  })
}
