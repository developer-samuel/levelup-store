import { handleScrollToTopVisibility } from '@/ts/presentation/widgets/scroll_top/_handlers/scrollToTopHandler'

export function attachScrollListener(element: HTMLElement): void {
  window.addEventListener('scroll', () => handleScrollToTopVisibility(element))
}
