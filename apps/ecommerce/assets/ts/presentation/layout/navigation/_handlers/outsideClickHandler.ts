import type { NavigationInstance } from '@/ts/presentation/layout/navigation/types'

export function handleOutsideClick(event: MouseEvent, instance: NavigationInstance): void {
  const { mobileContainer, mobileIcon } = instance
  if (!mobileContainer?.classList.contains('visible')) return

  const mobileContent = mobileContainer.querySelector('.navigation__mobile-content')
  const searchPanel = document.querySelector('.search-panel')
  const target = event.target as Node

  if (!mobileContent?.contains(target) && !mobileIcon?.contains(target) && !searchPanel?.contains(target)) {
    mobileContainer.classList.remove('visible')
  }
}
