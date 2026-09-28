import type { NavigationInstance } from '@/ts/presentation/layout/navigation/types'
import { handleOutsideClick } from '@/ts/presentation/layout/navigation/_handlers/outsideClickHandler'

export function attachOutsideClickListener(instance: NavigationInstance): void {
  document.addEventListener('click', (e) => handleOutsideClick(e, instance))
}
