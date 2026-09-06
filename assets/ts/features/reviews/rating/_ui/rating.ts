import type { RatingType } from '@/ts/features/reviews/rating/types'

const ACTIVE_CLASS = 'reviews__card-item-row-right-rating--active'

function updateRatingCount(el: HTMLElement | null, delta: number): void {
  if (!el) return

  const text = el.querySelector<HTMLElement>('.reviews__card-item-row-right-rating-count')
  if (!text) return

  const current = parseInt(text.textContent ?? '0', 10)
  text.textContent = String(Math.max(0, current + delta))
}

function applyRatingChange(
  clicked: HTMLElement | null,
  other: HTMLElement | null,
  otherActive: boolean,
  clickedIsActive: boolean,
): void {
  if (!clickedIsActive) updateRatingCount(clicked, 1)
  if (otherActive) updateRatingCount(other, -1)
  clicked?.classList.add(ACTIVE_CLASS)
}

/** Updates the like/dislike button states and counters */
export function updateReviewRating(
  like: HTMLElement | null,
  dislike: HTMLElement | null,
  typeToSend: RatingType,
  clickedIsActive: boolean,
): void {
  const likeActive = like?.classList.contains(ACTIVE_CLASS) ?? false
  const dislikeActive = dislike?.classList.contains(ACTIVE_CLASS) ?? false

  like?.classList.remove(ACTIVE_CLASS)
  dislike?.classList.remove(ACTIVE_CLASS)

  if (clickedIsActive && typeToSend === null) {
    if (likeActive) updateRatingCount(like, -1)
    if (dislikeActive) updateRatingCount(dislike, -1)
    return
  }

  if (typeToSend === 'like') applyRatingChange(like, dislike, dislikeActive, clickedIsActive)
  else if (typeToSend === 'dislike') applyRatingChange(dislike, like, likeActive, clickedIsActive)
}

/** Reads rating type from dataset and highlights the active button */
export function syncRatingHighlight(container: HTMLElement): void {
  const raw = container.dataset.ratingType
  const type: RatingType = raw === 'like' || raw === 'dislike' ? raw : null
  highlightReviewRating(container, type)
}

/** Highlights the active like/dislike button based on current rating type */
export function highlightReviewRating(container: HTMLElement, type: RatingType): void {
  const like = container.querySelector<HTMLElement>('#reviews-like')
  const dislike = container.querySelector<HTMLElement>('#reviews-dislike')

  const toggleHighlight = (element: HTMLElement | null, active: boolean): void => {
    const icon = element?.querySelector<HTMLElement>('.reviews__card-item-row-right-rating-icon')
    if (!icon) return

    icon.classList.toggle('reviews__card-item-row-right-rating-icon--active', active)
  }

  toggleHighlight(like, type === 'like')
  toggleHighlight(dislike, type === 'dislike')
}
