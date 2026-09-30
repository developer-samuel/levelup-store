import type { NavigationInstance } from '@/ts/presentation/layout/navigation/types'
import { restoreUrlActive } from '@/ts/presentation/layout/navigation/_ui/url'

function toggleMobileIcon(mobileIcon: HTMLImageElement | null, mobileContainer: HTMLElement | null): void {
  if (!mobileIcon || !mobileContainer) return

  const visible = mobileContainer.classList.contains('visible')

  if (visible) {
    mobileIcon.src = mobileIcon.src.replace('icons/app/menu.png', 'icons/actions/close.png')
    mobileIcon.alt = 'Close'
    mobileIcon.style.padding = '2px'
  } else {
    mobileIcon.src = mobileIcon.src.replace('icons/actions/close.png', 'icons/app/menu.png')
    mobileIcon.alt = 'Menu'
    mobileIcon.style.padding = '1px'
  }
}

export function checkMobileVisibility(instance: NavigationInstance): boolean {
  if (!instance.mobileContainer) return false

  return instance.mobileContainer.classList.contains('visible')
}

export function setMobileItemVisibility(idFromUrl: string | null, isMobileVisible: () => boolean): void {
  if (!idFromUrl) return

  const mobileItem = document.getElementById(`mobile-${idFromUrl}`)
  if (!mobileItem) return

  if (isMobileVisible()) {
    mobileItem.classList.add('visible')
  } else {
    mobileItem.classList.remove('visible')
  }
}

export function updateMobileItemVisibility(instance: NavigationInstance): void {
  toggleMobileIcon(instance.mobileIcon, instance.mobileContainer)
  restoreUrlActive(instance)
  setMobileItemVisibility(instance.idFromUrl, () => checkMobileVisibility(instance))
}
