import { handleResize } from '@/ts/features/products/list/_handlers/resizeHandler'

export function attachResizeListener(productFilter: HTMLElement | null): (() => void) | void {
  if (!productFilter) return

  const lastWidth = { value: window.innerWidth }
  const handler = (): void => handleResize(productFilter, lastWidth)

  window.addEventListener('resize', handler)

  return () => window.removeEventListener('resize', handler)
}
