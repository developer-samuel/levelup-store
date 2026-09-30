import { BREAKPOINT_XL } from '@/ts/shared/constants/breakpoints'

import { hide, isVisible } from '@/ts/features/products/list/_ui/visibility'

export function attachDocumentClickListener(
  mobileFilterBtn: HTMLElement | null,
  productFilter: HTMLElement | null,
): void {
  if (!mobileFilterBtn || !productFilter) return

  document.addEventListener('click', (e: MouseEvent) => {
    if (window.innerWidth >= BREAKPOINT_XL) return

    const target = e.target instanceof Node ? e.target : null
    const loadingEl = document.querySelector('.loading')

    if (
      !productFilter.contains(target) &&
      !mobileFilterBtn.contains(target) &&
      !loadingEl?.contains(target) &&
      isVisible(productFilter)
    ) {
      hide(productFilter)
    }
  })
}
