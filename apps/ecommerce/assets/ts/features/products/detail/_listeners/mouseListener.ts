import type { ImageCarouselInstance } from '@/ts/features/products/detail/types'
import { handleDragStart, handleDragEnd, handleDragAction } from '@/ts/features/products/detail/_handlers/dragHandler'

export function attachMouseListeners(track: HTMLElement, carousel: ImageCarouselInstance): void {
  track.addEventListener('mousedown', (e: MouseEvent) => handleDragStart(e, carousel))
  track.addEventListener('mouseup', () => handleDragEnd(carousel))
  track.addEventListener('mouseleave', () => handleDragEnd(carousel))
  track.addEventListener('mousemove', (e: MouseEvent) => handleDragAction(e, carousel))
}
