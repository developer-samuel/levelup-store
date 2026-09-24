import { BREAKPOINT_XL } from '@/ts/shared/constants/breakpoints'

import { toggle } from '@/ts/features/products/list/_ui/visibility'

export function handleResize(productFilter: HTMLElement, lastWidth: { value: number }): void {
  const currentWidth = window.innerWidth
  if (currentWidth === lastWidth.value) return

  const wasDesktop = lastWidth.value >= BREAKPOINT_XL
  const isDesktop = currentWidth >= BREAKPOINT_XL

  lastWidth.value = currentWidth

  if (!wasDesktop && isDesktop) {
    toggle(productFilter, true)
  } else if (wasDesktop && !isDesktop) {
    toggle(productFilter, false)
  }
}
