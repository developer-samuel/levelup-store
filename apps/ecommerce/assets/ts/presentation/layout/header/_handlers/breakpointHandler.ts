import type { HeaderInstance } from '@/ts/presentation/layout/header/types'
import { updateLogo } from '@/ts/presentation/layout/header/_ui/logo'
import { dispatchHeaderToggle } from '@/ts/presentation/layout/header/_events/toggle'

export function handleBreakpointChange(instance: HeaderInstance, isDesktop: boolean): void {
  instance.mobileNavigation?.classList.remove('visible')

  if (isDesktop) {
    const header = instance.header

    if (header) {
      header.classList.remove('header--hidden')
      dispatchHeaderToggle(header, false)
    }
  }

  updateLogo(instance, isDesktop)
}
