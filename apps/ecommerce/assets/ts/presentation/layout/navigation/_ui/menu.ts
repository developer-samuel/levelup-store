import { queryAll } from '@/ts/shared/utils/dom/query'

import type { NavigationInstance } from '@/ts/presentation/layout/navigation/types'
import { updateMobileItemVisibility } from '@/ts/presentation/layout/navigation/_ui/mobileVisibility'
import { restoreUrlActive } from '@/ts/presentation/layout/navigation/_ui/url'

export function hideMenu(instance: NavigationInstance): void {
  const overNavList = instance.navList?.matches(':hover') ?? false
  const overNavMenu = instance.navMenu?.matches(':hover') ?? false

  if (!overNavList && !overNavMenu) {
    instance.activeMenu = false
    instance.resetMenu()
    updateMobileItemVisibility(instance)
  }
}

/**
 * Resets all menu visibility when the cursor is not over the nav list or menu.
 * Preserves the URL-derived active item.
 */
export function resetMenu(instance: NavigationInstance): void {
  const overNavList = instance.navList?.matches(':hover') ?? false
  const overNavMenu = instance.navMenu?.matches(':hover') ?? false

  if (overNavList || overNavMenu) return

  instance.navMenu?.classList.remove('visible')

  queryAll<HTMLElement>('.navigation__menu-submenu.visible').forEach((el) => el.classList.remove('visible'))

  queryAll<HTMLElement>('.navigation__list-item.visible').forEach((el) => {
    if (el.id !== instance.idFromUrl) {
      el.classList.remove('visible')
    }
  })

  queryAll<HTMLElement>('.navigation__mobile-item.visible').forEach((el) => {
    if (el.id !== `mobile-${instance.idFromUrl}`) {
      el.classList.remove('visible')
    }
  })

  instance.activeMenu = null

  restoreUrlActive(instance)
}
