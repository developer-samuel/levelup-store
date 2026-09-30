import { scrollToTop } from '@/ts/shared/utils/scroll'

import { attachScrollListener } from '@/ts/presentation/widgets/scroll_top/_listeners/scrollListener'

export function bindScrollToTop(element: HTMLElement): void {
  attachScrollListener(element)
  element.addEventListener('click', () => scrollToTop())
}
