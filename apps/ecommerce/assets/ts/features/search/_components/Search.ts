import { query } from '@/ts/shared/utils/dom/query'

import type { SearchInstance } from '@/ts/features/search/types'
import { getSearchElements } from '@/ts/features/search/_utils/elements'
import { updateSearchIcons } from '@/ts/features/search/_ui/panel'
import { updateSearch } from '@/ts/features/search/_ui/search'
import { hide } from '@/ts/features/search/_ui/visibility'
import { attachOutsideClickListener } from '@/ts/features/search/_listeners/outsideClickListener'
import { attachHeaderToggleListener } from '@/ts/features/search/_listeners/headerToggleListener'
import { observeResize } from '@/ts/features/search/_observers/resizeObserver'
import { setupButton } from '@/ts/features/search/_interactions/button'
import { bindMobileHeader } from '@/ts/features/search/_interactions/mobileHeader'
import { bindSearchFlow, setSearchTermFlow, performSearchFlow } from '@/ts/features/search/_interactions/searchFlow'
import { bindSearchQuery } from '@/ts/features/search/_interactions/searchQuery'

type SearchOptions = {
  panelSelector?: string
  contentSelector?: string
}

export default class Search implements SearchInstance {
  readonly panel: HTMLElement
  readonly content: HTMLElement
  readonly inputs: HTMLInputElement[]
  readonly icons: HTMLElement[]
  readonly closes: HTMLElement[]
  readonly prevWidthRef: { value: number }
  readonly userButton: HTMLElement | null
  readonly searchButton: HTMLElement | null
  readonly mobileSearchButton: HTMLElement | null
  readonly mobileClose: HTMLElement | null
  readonly mobileCloseImage: HTMLElement | null
  readonly headerCloseImage: HTMLElement | null
  currentSearchTerm: string
  isClosed: boolean

  constructor({ panelSelector = '.search-panel', contentSelector = '.search-panel__content' }: SearchOptions = {}) {
    const panel = query<HTMLElement>(panelSelector)
    const contentEl = query<HTMLElement>(contentSelector)

    if (!panel || !contentEl) {
      throw new Error('Search: Panel or content element not found')
    }

    this.panel = panel
    this.content = contentEl

    const elements = getSearchElements()

    this.inputs = Array.from(elements.inputs)
    this.icons = Array.from(elements.icons)
    this.closes = Array.from(elements.closes)

    this.userButton = elements.userButton
    this.searchButton = elements.searchButton
    this.mobileSearchButton = elements.mobileSearchButton
    this.mobileClose = elements.mobileClose
    this.mobileCloseImage = elements.mobileCloseImage
    this.headerCloseImage = elements.headerCloseImage

    this.currentSearchTerm = ''
    this.isClosed = false
    this.prevWidthRef = { value: window.innerWidth }

    this.init()
  }

  private init(): void {
    const setSearchTerm = (val: string): void => setSearchTermFlow(this, val)
    const updateUI = (val?: string): void => updateSearch(this, val ?? '')
    const performSearch = (): void => performSearchFlow(this)

    bindSearchFlow({
      inputs: this.inputs,
      instance: this,
      closes: this.closes,
      performSearch,
      updateUI,
      setSearchTerm,
    })

    setupButton({
      searchButton: this.searchButton,
      mobileSearchButton: this.mobileSearchButton,
      userButton: this.userButton,
      clearElements: [this.mobileClose, this.mobileCloseImage, this.headerCloseImage],
      performSearch,
      clearSearchInputs: {
        inputs: this.inputs,
        setSearchTerm,
        updateContent: (html: string): void => updateSearch(this, html),
        hidePanel: (): void => {
          this.isClosed = true
          hide(this.panel)
        },
        updateUI,
        setIsClosed: (val: boolean): void => {
          this.isClosed = val
        },
      },
      hidePanel: (): void => hide(this.panel),
    })

    attachOutsideClickListener({ instance: this, inputs: this.inputs })
    attachHeaderToggleListener(this.panel)

    observeResize({
      instance: this,
      inputs: this.inputs,
      prevWidthRef: this.prevWidthRef,
    })

    bindSearchQuery(this)
    bindMobileHeader(this)

    updateSearchIcons(this)
  }
}
