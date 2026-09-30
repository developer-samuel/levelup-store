import { handleScroll } from '@/ts/presentation/layout/header/_handlers/scrollHandler'

export function attachScrollListener(headerMain: HTMLElement | null): void {
  if (!headerMain) return

  window.addEventListener('scroll', (): void => handleScroll(headerMain))
}
