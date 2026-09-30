import { BREAKPOINT_LG } from '@/ts/shared/constants/breakpoints'

import type { HeaderInstance } from '@/ts/presentation/layout/header/types'
import { handleBreakpointChange } from '@/ts/presentation/layout/header/_handlers/breakpointHandler'

const DESKTOP_MEDIA = window.matchMedia(`(min-width: ${BREAKPOINT_LG}px)`)

export function attachBreakpointListener(instance: HeaderInstance): void {
  handleBreakpointChange(instance, DESKTOP_MEDIA.matches)

  DESKTOP_MEDIA.addEventListener('change', (e) => handleBreakpointChange(instance, e.matches))
}
