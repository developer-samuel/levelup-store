import type { ResizeHandlerOptions } from '@/ts/features/search/types'
import { handleSearchResize } from '@/ts/features/search/_handlers/resizeHandler'

export function observeResize(handlerOptions: ResizeHandlerOptions): void {
  const observer = new ResizeObserver((entries) => {
    const currentWidth = entries[0]?.contentRect.width ?? window.innerWidth
    handleSearchResize(handlerOptions, currentWidth)
  })

  observer.observe(document.body)
}
