import type { NavigationInstance } from '@/ts/presentation/layout/navigation/types'
import { stopObservers } from '@/ts/presentation/layout/navigation/_utils/observers'
import { updateMobileItemVisibility } from '@/ts/presentation/layout/navigation/_ui/mobileVisibility'
import { observeMobileVisibility } from '@/ts/presentation/layout/navigation/_observers/mobileVisibility'

export function setupObservers(instance: NavigationInstance): void {
  const observers = observeMobileVisibility(instance.mobileContainer, () => updateMobileItemVisibility(instance))

  if (observers) {
    instance.mutationObserver = observers.mutationObserver
    instance.resizeObserver = observers.resizeObserver
  }
}

export function stopMobileObservers(instance: NavigationInstance): void {
  stopObservers({
    ...(instance.mutationObserver && { mutationObserver: instance.mutationObserver }),
    ...(instance.resizeObserver && { resizeObserver: instance.resizeObserver }),
  })
}
