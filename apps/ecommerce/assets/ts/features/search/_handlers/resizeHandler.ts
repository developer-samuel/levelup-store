import { BREAKPOINT_LG } from '@/ts/shared/constants/breakpoints'

import type { ResizeHandlerOptions } from '@/ts/features/search/types'
import { syncInputValues } from '@/ts/features/search/_utils/input'
import { updateSearchIcons } from '@/ts/features/search/_ui/panel'
import { updateContent } from '@/ts/features/search/_ui/content'
import { hide } from '@/ts/features/search/_ui/visibility'
import { getAbortSignal } from '@/ts/features/search/_state/abortSignal'

export function handleSearchResize(options: ResizeHandlerOptions, currentWidth: number): void {
  const { instance, inputs, prevWidthRef } = options

  const prevWidth = prevWidthRef?.value ?? currentWidth

  const crossedToMobile = prevWidth >= BREAKPOINT_LG && currentWidth < BREAKPOINT_LG
  const crossedToDesktop = prevWidth < BREAKPOINT_LG && currentWidth >= BREAKPOINT_LG

  if (crossedToMobile || crossedToDesktop) {
    getAbortSignal()
    hide(instance.panel)
    instance.isClosed = true
    updateContent(instance.content, '')
    updateSearchIcons(instance, currentWidth)
  }

  const term = instance.currentSearchTerm
  if (term) syncInputValues(inputs, term)

  prevWidthRef.value = currentWidth
}
