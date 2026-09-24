import { queryAll } from '@/ts/shared/utils/dom/query'

import type { NavigationInstance } from '@/ts/presentation/layout/navigation/types'
import { hideMenu } from '@/ts/presentation/layout/navigation/_ui/menu'
import { updateMobileItemVisibility } from '@/ts/presentation/layout/navigation/_ui/mobileVisibility'
import { hideAllNavItems, showNavItems } from '@/ts/presentation/layout/navigation/_ui/navItems'
import { showSubmenu, activateSubmenu } from '@/ts/presentation/layout/navigation/_ui/submenu'
import { hideUserDropdown } from '@/ts/presentation/layout/navigation/_ui/userDropdown'

export function handleMouseOver(instance: NavigationInstance, e: MouseEvent): void {
  const target = e.target instanceof Element ? e.target : null
  const item =
    target?.closest<HTMLElement>('.navigation__list-item') ??
    target?.closest<HTMLElement>('.navigation__mobile-item') ??
    null

  if (!item) return

  const id = item.id.replace('mobile-', '')

  hideAllNavItems()

  if (queryAll('.header__main-user-dropdown.visible').length) {
    hideUserDropdown()
  }

  const shown = showNavItems(id)
  if (!shown) return

  if (id !== 'discount-item') {
    showSubmenu(instance, id)
  }
}

export function handleMouseOut(instance: NavigationInstance, e: MouseEvent): void {
  const target = e.target instanceof Element ? e.target : null
  const item = target?.closest<HTMLElement>('.navigation__list-item') ?? null
  const relatedTarget = e.relatedTarget instanceof Node ? e.relatedTarget : null

  if (item && (!relatedTarget || (!item.contains(relatedTarget) && !instance.navMenu?.contains(relatedTarget)))) {
    hideMenu(instance)
  }
}

export function handleNavListItemEnter(instance: NavigationInstance, e: MouseEvent): void {
  const id = e.currentTarget instanceof HTMLElement ? e.currentTarget.id : null
  if (!id) return

  instance.activeMenu = true
  activateSubmenu(instance, id)
  updateMobileItemVisibility(instance)
}

export function handleHideMenu(instance: NavigationInstance): void {
  instance.resetMenu()
  instance.activeMenu = false
}

export function handleKeepActiveMenu(instance: NavigationInstance): void {
  if (instance.activeMenu) {
    instance.navMenu?.classList.add('visible')
  }
}

export function handleMenuLeave(instance: NavigationInstance): void {
  instance.activeMenu = false
  instance.resetMenu()
  updateMobileItemVisibility(instance)
}
