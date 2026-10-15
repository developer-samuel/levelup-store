import type { HeaderInstance } from '@/ts/presentation/layout/header/types'

export function toggleMobileNavigation(instance: HeaderInstance): void {
  const isOpen = instance.mobileNavigation?.classList.toggle('visible') ?? false
  document.body.classList.toggle('nav--open', isOpen)

  if (instance.searchPanel) {
    instance.searchPanel.style.display = 'none'
    document.body.classList.remove('search--open')
  }
}
