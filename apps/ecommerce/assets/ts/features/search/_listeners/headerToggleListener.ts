import { toggleClass } from '@/ts/shared/utils/dom/classes'

import { HEADER_TOGGLE } from '@/ts/presentation/layout/header/_events/toggle'

export function attachHeaderToggleListener(panel: HTMLElement): void {
  document.addEventListener(HEADER_TOGGLE, (e) => {
    const { hidden } = (e as CustomEvent<{ hidden: boolean }>).detail

    toggleClass(panel, 'search-panel--header-hidden', hidden)
  })
}
