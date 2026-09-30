import type { ImageCarouselInstance } from '@/ts/features/products/detail/types'
import { handleDragStart, handleDragEnd, handleDragAction } from '@/ts/features/products/detail/_handlers/dragHandler'

export function attachTouchListeners(track: HTMLElement, carousel: ImageCarouselInstance): void {
  track.addEventListener('touchstart', (e: TouchEvent) => handleDragStart(e, carousel))
  track.addEventListener('touchend', () => handleDragEnd(carousel))
  track.addEventListener('touchcancel', () => handleDragEnd(carousel))
  track.addEventListener('touchmove', (e: TouchEvent) => handleDragAction(e, carousel), { passive: false })
}
