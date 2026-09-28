import { BREAKPOINT_LG } from '@/ts/shared/constants/breakpoints'
import { query } from '@/ts/shared/utils/dom/query'

import type { SearchInstance } from '@/ts/features/search/types'
import { LOADING_HTML } from '@/ts/features/search/constants'
import { hasInputValue } from '@/ts/features/search/_utils/input'
import { updateContent } from '@/ts/features/search/_ui/content'
import { updateInstanceCloseIcons } from '@/ts/features/search/_ui/icons'
import { show, hide, isVisible } from '@/ts/features/search/_ui/visibility'

function updatePanelVisibility(instance: SearchInstance, hasText: boolean, hasQueryParam: boolean): void {
  if (hasText && !hasQueryParam) {
    if (!isVisible(instance.panel)) {
      updateContent(instance.content, LOADING_HTML)
      show(instance.panel)
    }
  } else if (!hasText) {
    if (isVisible(instance.panel)) {
      hide(instance.panel)
    }
  }
}

function updateMobileIcon(hasText: boolean, isMobile: boolean): void {
  const mobileIcon = query<HTMLElement>('.navigation__mobile-icon')
  if (mobileIcon) {
    mobileIcon.style.display = !hasText && isMobile ? 'flex' : 'none'
  }
}

function updateMobileClose(instance: SearchInstance, hasText: boolean, isMobile: boolean): void {
  if (instance.mobileClose) {
    instance.mobileClose.style.display = hasText && isMobile ? 'flex' : 'none'
  }
}

function syncIcons(instance: SearchInstance, hasText: boolean, isMobile: boolean): void {
  updateInstanceCloseIcons(instance)
  updateMobileIcon(hasText, isMobile)
  updateMobileClose(instance, hasText, isMobile)
}

export function updateSearchUI(instance: SearchInstance): void {
  const hasText = hasInputValue(instance)
  const isMobile = window.innerWidth < BREAKPOINT_LG
  const hasQueryParam = new URLSearchParams(window.location.search).has('query')

  updatePanelVisibility(instance, hasText, hasQueryParam)
  syncIcons(instance, hasText, isMobile)
}

export function updateSearchIcons(instance: SearchInstance, currentWidth?: number): void {
  const isMobile = (currentWidth ?? window.innerWidth) < BREAKPOINT_LG

  syncIcons(instance, hasInputValue(instance), isMobile)
}
