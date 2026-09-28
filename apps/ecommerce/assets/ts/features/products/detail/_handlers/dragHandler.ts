import type { ImageCarouselInstance } from '@/ts/features/products/detail/types'
import { getPositionX, getPositionY } from '@/ts/features/products/detail/_utils/touchPosition'
import { applyDraggingStyles, removeDraggingStyles } from '@/ts/features/products/detail/_ui/dragging'
import { slideTo, setSliderPosition } from '@/ts/features/products/detail/_ui/slider'

const SWIPE_THRESHOLD = 50

export function handleDragStart(event: MouseEvent | TouchEvent, carousel: ImageCarouselInstance): void {
  carousel.isDragging = true
  carousel.isHorizontalSwipe = null
  carousel.startX = getPositionX(event)
  carousel.startY = getPositionY(event)

  cancelAnimationFrame(carousel.animationID)

  applyDraggingStyles(carousel.track)
}

export function handleDragAction(event: MouseEvent | TouchEvent, carousel: ImageCarouselInstance): void {
  if (!carousel.isDragging) return

  const currentX = getPositionX(event)
  const currentY = getPositionY(event)
  const diffX = currentX - carousel.startX
  const diffY = currentY - carousel.startY

  if (carousel.isHorizontalSwipe === null && (Math.abs(diffX) > 5 || Math.abs(diffY) > 5)) {
    carousel.isHorizontalSwipe = Math.abs(diffX) > Math.abs(diffY)
  }

  if (!carousel.isHorizontalSwipe) return

  event.preventDefault()
  carousel.currentTranslate = carousel.prevTranslate + diffX
  setSliderPosition(carousel)
}

export function handleDragEnd(carousel: ImageCarouselInstance): void {
  if (!carousel.isDragging) return

  carousel.isDragging = false
  carousel.isHorizontalSwipe = null
  removeDraggingStyles(carousel.track)

  const movedBy = carousel.currentTranslate - carousel.prevTranslate

  if (movedBy < -SWIPE_THRESHOLD) {
    carousel.currentIndex = (carousel.currentIndex + 1) % carousel.total
  } else if (movedBy > SWIPE_THRESHOLD) {
    carousel.currentIndex = (carousel.currentIndex - 1 + carousel.total) % carousel.total
  }

  slideTo(carousel, carousel.currentIndex)
}
