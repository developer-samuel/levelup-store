import { query } from '@/ts/shared/utils/dom/query'

import type { HeaderInstance } from '@/ts/presentation/layout/header/types'
import { attachBreakpointListener } from '@/ts/presentation/layout/header/_listeners/breakpointListener'
import { attachMobileIconListener } from '@/ts/presentation/layout/header/_listeners/clickListener'
import { attachScrollListener } from '@/ts/presentation/layout/header/_listeners/scrollListener'

export class Header implements HeaderInstance {
  readonly header: HTMLElement | null
  readonly mobileNavigation: HTMLElement | null
  readonly searchPanel: HTMLElement | null
  readonly logoImage: HTMLImageElement | null
  private readonly headerMain: HTMLElement | null
  private readonly mobileIcon: HTMLElement | null

  constructor(iconSelector: string, navigationSelector: string, searchPanelSelector: string) {
    this.mobileIcon = query<HTMLElement>(iconSelector)
    this.mobileNavigation = query<HTMLElement>(navigationSelector)
    this.searchPanel = query<HTMLElement>(searchPanelSelector)
    this.headerMain = query<HTMLElement>('.header__main')
    this.header = this.headerMain?.parentElement ?? null
    this.logoImage = document.getElementById('logo') as HTMLImageElement | null

    this.initListeners()
  }

  private initListeners(): void {
    attachMobileIconListener(this.mobileIcon, this)
    attachScrollListener(this.headerMain)
    attachBreakpointListener(this)
  }
}
