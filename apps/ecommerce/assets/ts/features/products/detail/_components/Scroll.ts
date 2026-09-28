import { query } from '@/ts/shared/utils/dom/query'

import { handleScroll } from '@/ts/features/products/detail/_handlers/scrollHandler'
import type { ScrollComponent } from '@/ts/features/products/detail/types'

export default class BaseScroll {
  protected readonly button: HTMLElement | null
  protected readonly target: HTMLElement | null

  constructor(buttonSelector: string | HTMLElement, targetSelector: string | HTMLElement) {
    this.button = typeof buttonSelector === 'string' ? query<HTMLElement>(buttonSelector) : buttonSelector

    this.target = typeof targetSelector === 'string' ? query<HTMLElement>(targetSelector) : targetSelector

    this.init()
  }

  protected init(): void {
    if (!this.button || !this.target) return

    const component: ScrollComponent = { button: this.button, target: this.target }
    this.button.addEventListener('click', (event: MouseEvent) => handleScroll(event, component))
  }
}
