import { query, queryAll } from '@/ts/shared/utils/dom/query'

import type { ImageCarouselInstance } from '@/ts/features/products/detail/types'

/**
 * Updates the active class on thumbnails and pagination dots.
 *
 * Scrolls the thumbnail list so the active item is centered.
 */
function updateActiveThumb(carousel: ImageCarouselInstance): void {
  carousel.thumbs.forEach((item, i) => {
    const isActive = i === carousel.currentIndex
    item.classList.toggle('product-detail__gallery-item--active', isActive)

    if (isActive) {
      const list = item.parentElement
      if (!list) return

      const itemCenter = item.offsetLeft + item.offsetWidth / 2
      const listCenter = list.offsetWidth / 2
      list.scrollTo({ left: itemCenter - listCenter, behavior: 'smooth' })
    }
  })

  const dots = queryAll<HTMLElement>('.product-detail__gallery-image-dots-item')
  dots.forEach((dot, i) => {
    dot.classList.toggle('product-detail__gallery-image-dots-item--active', i === carousel.currentIndex)
  })
}

/** Recalculates the container width and repositions the slider to the current index */
export function updateWidth(carousel: ImageCarouselInstance): void {
  const container = query<HTMLElement>('.product-detail__gallery-image')
  if (!container) return

  carousel.containerWidth = container.offsetWidth
  slideTo(carousel, carousel.currentIndex, false)
}

/**
 * Slides the carousel to the given index with optional animation.
 *
 * Wraps around on overflow - going past the last slide returns to index 0, and vice-versa.
 */
export function slideTo(carousel: ImageCarouselInstance, index: number, animate = true): void {
  if (index < 0) index = carousel.total - 1
  else if (index >= carousel.total) index = 0

  carousel.currentIndex = index
  carousel.track.style.transition = animate ? 'transform 0.3s ease' : 'none'

  carousel.currentTranslate = -index * carousel.containerWidth
  carousel.prevTranslate = carousel.currentTranslate

  setSliderPosition(carousel)
  updateActiveThumb(carousel)
}

export function setSliderPosition(carousel: ImageCarouselInstance): void {
  carousel.track.style.transform = `translateX(${carousel.currentTranslate}px)`
}
