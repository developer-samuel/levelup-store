import type { RatingType } from '@/ts/features/reviews/rating/types'

export function ratingClickedType(clicked: HTMLElement | null): RatingType {
  if (!clicked) return null

  return clicked.id === 'reviews-like' ? 'like' : 'dislike'
}

export function ratingIsActive(clicked: HTMLElement): boolean {
  return clicked.classList.contains('reviews__card-item-row-right-rating--active')
}
