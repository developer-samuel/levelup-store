import type { ImageCarouselInstance } from '@/ts/features/products/detail/types'
import { slideTo } from '@/ts/features/products/detail/_ui/slider'

export function attachArrowClickListener(
  arrow: HTMLElement | null,
  carousel: ImageCarouselInstance,
  direction: 1 | -1,
): void {
  if (!arrow) return

  arrow.addEventListener('click', () => slideTo(carousel, carousel.currentIndex + direction))
}
