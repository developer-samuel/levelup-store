import { toggleClass } from '@/ts/shared/utils/dom/classes'

import { HEADER_TOGGLE } from '@/ts/presentation/layout/header/_events/toggle'

export function attachHeaderToggleListener(
  mobileContainer: HTMLElement | null,
  navContainer: HTMLElement | null,
): void {
  document.addEventListener(HEADER_TOGGLE, (e) => {
    const { hidden } = (e as CustomEvent<{ hidden: boolean }>).detail

    toggleClass(mobileContainer, 'navigation__mobile--header-hidden', hidden)
    toggleClass(navContainer, 'navigation--header-hidden', hidden)
  })
}
