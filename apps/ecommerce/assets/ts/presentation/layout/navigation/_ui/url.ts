import { BREAKPOINT_LG } from '@/ts/shared/constants/breakpoints'

import type { NavigationInstance } from '@/ts/presentation/layout/navigation/types'

export function restoreUrlActive(instance: NavigationInstance): void {
  const id = instance.idFromUrl
  if (!id) return

  const isMobile = window.innerWidth < BREAKPOINT_LG

  if (!isMobile) {
    document.getElementById(id)?.classList.add('visible')
    document.getElementById(`mobile-${id}`)?.classList.remove('visible')
  } else {
    document.getElementById(`mobile-${id}`)?.classList.add('visible')
    document.getElementById(id)?.classList.remove('visible')
  }
}
