import { query } from '@/ts/shared/utils/dom/query'

import type { NavigationInstance } from '@/ts/presentation/layout/navigation/types'
import { resetMenu } from '@/ts/presentation/layout/navigation/_ui/menu'
import { updateMobileItemVisibility } from '@/ts/presentation/layout/navigation/_ui/mobileVisibility'
import {
  attachNavListMouseListeners,
  attachNavMenuMouseListeners,
  attachNavScrollListener,
  attachNavResizeListener,
} from '@/ts/presentation/layout/navigation/_listeners/domListener'
import { attachOutsideClickListener } from '@/ts/presentation/layout/navigation/_listeners/outsideClickListener'
import { attachHeaderToggleListener } from '@/ts/presentation/layout/navigation/_listeners/headerToggleListener'
import { activateMenu } from '@/ts/presentation/layout/navigation/_interactions/menu'
import { setupObservers } from '@/ts/presentation/layout/navigation/_interactions/observers'

export class Navigation implements NavigationInstance {
  readonly navList: HTMLElement | null
  readonly navMenu: HTMLElement | null
  readonly navContainer: HTMLElement | null
  readonly mobileContainer: HTMLElement | null
  readonly mobileIcon: HTMLImageElement | null
  readonly resetMenu: () => void
  activeMenu: HTMLElement | boolean | null = false
  idFromUrl: string | null = null
  currentHoveredItem: string | null = null
  mutationObserver: MutationObserver | null = null
  resizeObserver: ResizeObserver | null = null

  constructor(navSelector: string) {
    this.navList = query<HTMLElement>(navSelector)
    this.navMenu = query<HTMLElement>('.navigation__menu')
    this.navContainer = query<HTMLElement>('.navigation')
    this.mobileContainer = query<HTMLElement>('.navigation__mobile')
    this.mobileIcon = query<HTMLImageElement>('#header-mobile-icon')

    this.resetMenu = (): void => resetMenu(this)

    this.initListeners()

    activateMenu(this)
    setupObservers(this)
  }

  private initListeners(): void {
    if (this.navList) {
      attachNavScrollListener(this.navList)
      attachNavResizeListener(this.navList)
    }

    attachNavListMouseListeners(this.navList, this)
    attachNavMenuMouseListeners(this.navMenu, this)
    attachOutsideClickListener(this)
    attachHeaderToggleListener(this.mobileContainer, this.navContainer)

    window.addEventListener('resize', (): void => updateMobileItemVisibility(this))
  }
}
