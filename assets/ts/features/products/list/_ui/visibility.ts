import { BREAKPOINT_XL } from '@/ts/shared/constants/breakpoints'

const ACTIVE_CLASS = 'products__filter--active'
const getBackdrop = (): HTMLElement | null => document.getElementById('filter-backdrop')

export function show(filter: HTMLElement | null): void {
  if (!filter) return

  filter.classList.add(ACTIVE_CLASS)
  document.body.style.overflow = 'hidden'

  const backdrop = getBackdrop()

  if (backdrop) backdrop.style.display = 'block'
}

export function hide(filter: HTMLElement | null): void {
  if (!filter) return

  filter.classList.remove(ACTIVE_CLASS)
  document.body.style.overflow = ''

  const backdrop = getBackdrop()

  if (backdrop) backdrop.style.display = 'none'
}

export function toggle(filter: HTMLElement | null, condition: boolean): void {
  if (!filter) return

  filter.classList.toggle(ACTIVE_CLASS, condition)

  const backdrop = getBackdrop()

  if (backdrop) backdrop.style.display = condition && window.innerWidth < BREAKPOINT_XL ? 'block' : 'none'
}

export function isVisible(element: HTMLElement | null): boolean {
  if (!element) return false

  return element.classList.contains(ACTIVE_CLASS)
}
