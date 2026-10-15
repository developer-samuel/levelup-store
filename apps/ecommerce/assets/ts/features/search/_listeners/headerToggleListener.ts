import { toggleClass } from '@/ts/shared/utils/dom/classes'

import { HEADER_TOGGLE } from '@/ts/presentation/layout/header/_events/toggle'

export function attachHeaderToggleListener(panel: HTMLElement): void {
  const isHidden = document.querySelector('.header')?.classList.contains('header--hidden') ?? false
  toggleClass(panel, 'search-panel--header-hidden', isHidden)

  document.addEventListener(HEADER_TOGGLE, (e) => {
    const { hidden } = (e as CustomEvent<{ hidden: boolean }>).detail

    toggleClass(panel, 'search-panel--header-hidden', hidden)
  })
}
