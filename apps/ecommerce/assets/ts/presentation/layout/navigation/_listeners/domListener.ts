import { BREAKPOINT_LG } from '@/ts/shared/constants/breakpoints'

import type { NavigationInstance } from '@/ts/presentation/layout/navigation/types'
import { toggleScrolledClass, syncScrolledClass } from '@/ts/presentation/layout/navigation/_ui/scroll'
import {
  handleMouseOver,
  handleMouseOut,
  handleNavListItemEnter,
  handleHideMenu,
  handleKeepActiveMenu,
  handleMenuLeave,
} from '@/ts/presentation/layout/navigation/_handlers/navHandler'

export function attachNavScrollListener(navList: HTMLElement): void {
  window.addEventListener('scroll', (): void => {
    if (window.innerWidth >= BREAKPOINT_LG) toggleScrolledClass(navList)
  })
}

export function attachNavResizeListener(navList: HTMLElement): void {
  window.addEventListener('resize', (): void => syncScrolledClass(navList))
}

export function attachNavListMouseListeners(navList: HTMLElement | null, instance: NavigationInstance): void {
  if (!navList) return

  navList.addEventListener('mouseover', (e) => handleMouseOver(instance, e))
  navList.addEventListener('mouseout', (e) => handleMouseOut(instance, e))
  navList.addEventListener('mouseleave', () => handleHideMenu(instance))

  const items = navList.querySelectorAll<HTMLElement>('.navigation__list-item')
  items.forEach((item) => {
    item.addEventListener('mouseenter', (e) => handleNavListItemEnter(instance, e))
  })
}

export function attachNavMenuMouseListeners(navMenu: HTMLElement | null, instance: NavigationInstance): void {
  if (!navMenu) return

  navMenu.addEventListener('mouseenter', () => handleKeepActiveMenu(instance))
  navMenu.addEventListener('mouseleave', () => handleMenuLeave(instance))
}
