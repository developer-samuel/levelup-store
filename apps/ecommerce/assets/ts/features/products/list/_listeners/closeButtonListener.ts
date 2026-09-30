import { BREAKPOINT_XL } from '@/ts/shared/constants/breakpoints'

import { hide } from '@/ts/features/products/list/_ui/visibility'

export function attachCloseButtonListener(closeBtn: HTMLElement | null, productFilter: HTMLElement | null): void {
  if (!closeBtn || !productFilter) return

  closeBtn.addEventListener('click', () => {
    if (window.innerWidth < BREAKPOINT_XL) hide(productFilter)
  })
}
