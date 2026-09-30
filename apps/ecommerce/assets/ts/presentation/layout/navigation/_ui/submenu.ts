import { query, queryAll } from '@/ts/shared/utils/dom/query'

import type { NavigationInstance } from '@/ts/presentation/layout/navigation/types'

/**
 * Hides all visible submenus, then shows the submenu for the given item.
 * Handles the discount-item edge case (no submenu).
 */
export function activateSubmenu(instance: NavigationInstance, itemId: string): void {
  queryAll<HTMLElement>('.navigation__menu-submenu.visible').forEach((el) => el.classList.remove('visible'))

  if (itemId === 'discount-item') {
    instance.navMenu?.classList.remove('visible')
    instance.currentHoveredItem = itemId
    return
  }

  const submenu = query<HTMLElement>(`#${itemId.replace('-item', '-menu')}`)

  if (submenu) {
    submenu.classList.add('visible')
    instance.navMenu?.classList.add('visible')
  }

  instance.currentHoveredItem = itemId
}

export function showSubmenu(instance: NavigationInstance, id: string): boolean {
  const mobileContainer = document.querySelector('.navigation__mobile')
  if (mobileContainer?.classList.contains('visible')) return false

  const targetMenuId = id.replace('-item', '-menu')
  const targetMenu = document.getElementById(targetMenuId)
  if (!targetMenu) return false

  instance.navMenu?.classList.add('visible')
  targetMenu.classList.add('visible')
  instance.activeMenu = targetMenu

  return true
}
