import { showScrollToTopVisible, hideScrollToTopVisible } from '@/ts/presentation/widgets/scroll_top/_ui/visibility'

const SCROLL_THRESHOLD_MOBILE = 75

export function handleScrollToTopVisibility(element: HTMLElement): void {
  if (window.scrollY >= SCROLL_THRESHOLD_MOBILE) {
    showScrollToTopVisible(element)
  } else {
    hideScrollToTopVisible(element)
  }
}
