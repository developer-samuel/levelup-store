import type { HeaderInstance } from '@/ts/presentation/layout/header/types'
import { toggleMobileNavigation } from '@/ts/presentation/layout/header/_ui/mobileNavigation'

export function attachMobileIconListener(mobileIcon: HTMLElement | null, instance: HeaderInstance): void {
  if (!mobileIcon) return

  mobileIcon.addEventListener('click', () => toggleMobileNavigation(instance))
}
