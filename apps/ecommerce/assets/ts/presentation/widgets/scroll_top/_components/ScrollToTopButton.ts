import { query } from '@/ts/shared/utils/dom/query'

import { bindScrollToTop } from '@/ts/presentation/widgets/scroll_top/_interactions/scrollToTop'

export default class ScrollToTopButton {
  private readonly element: HTMLElement | null

  constructor(selector: string) {
    this.element = query<HTMLElement>(selector)

    if (this.element) {
      bindScrollToTop(this.element)
    }
  }
}
