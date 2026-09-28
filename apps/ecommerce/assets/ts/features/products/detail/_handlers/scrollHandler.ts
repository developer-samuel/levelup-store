import { BREAKPOINT_LG } from '@/ts/shared/constants/breakpoints'

import type { ScrollComponent } from '@/ts/features/products/detail/types'

function scrollToViewWithOffset(target: Element | null): void {
  if (!target) return

  const offset = window.innerWidth >= BREAKPOINT_LG ? 150 : 0
  const top = target.getBoundingClientRect().top + window.scrollY - offset

  window.scrollTo({ top, behavior: 'smooth' })
}

export function handleScroll(event: Event, component: ScrollComponent): void {
  event.preventDefault()
  scrollToViewWithOffset(component.target)
}
