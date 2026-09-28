import type { HeaderInstance } from '@/ts/presentation/layout/header/types'

export function toggleMobileNavigation(instance: HeaderInstance): void {
  instance.mobileNavigation?.classList.toggle('visible')

  if (instance.searchPanel) {
    instance.searchPanel.style.display = 'none'
  }
}
