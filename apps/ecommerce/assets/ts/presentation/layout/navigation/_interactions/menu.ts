import type { NavigationInstance } from '@/ts/presentation/layout/navigation/types'
import { resolveNavId } from '@/ts/presentation/layout/navigation/_utils/navId'
import { checkMobileVisibility } from '@/ts/presentation/layout/navigation/_ui/mobileVisibility'
import { showDesktopNavItem, showMobileNavItem } from '@/ts/presentation/layout/navigation/_ui/navItems'

/**
 * Activates navigation items that correspond to the current URL.
 * Shows the desktop item always; shows the mobile item only when mobile nav is visible.
 */
export function activateMenu(instance: NavigationInstance): void {
  const id = resolveNavId()
  instance.idFromUrl = id
  if (!id) return

  showDesktopNavItem(id)

  if (checkMobileVisibility(instance)) {
    showMobileNavItem(id)
  }
}
