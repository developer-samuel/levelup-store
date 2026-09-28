import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import { getRatingElements } from '@/ts/features/reviews/rating/_ui/elements'
import { findClosestIcon, findClickableParent, getParentRow } from '@/ts/features/reviews/rating/_ui/finders'
import { updateReviewRating } from '@/ts/features/reviews/rating/_ui/rating'
import { ratingClickedType, ratingIsActive } from '@/ts/features/reviews/rating/_ui/states'
import { toggleReviewRating } from '@/ts/features/reviews/rating/_services/reviewRatingService'

let isToggling = false

export async function handleRatingToggle(event: MouseEvent, container: HTMLElement, reviewId: string): Promise<void> {
  event.preventDefault()
  event.stopPropagation()

  if (isToggling) return
  isToggling = true

  try {
    const icon = findClosestIcon(event)
    if (!icon) return

    const clicked = findClickableParent(icon, container)
    if (!clicked) return

    const clickedType = ratingClickedType(clicked)
    const parentRow = getParentRow(clicked)
    if (!parentRow) return

    const { likeEl, dislikeEl } = getRatingElements(parentRow)
    const clickedIsActive = ratingIsActive(clicked)
    const typeToSend = clickedIsActive ? null : clickedType

    updateReviewRating(likeEl, dislikeEl, typeToSend, clickedIsActive)

    await toggleReviewRating(reviewId, typeToSend)
  } catch {
    NotyfAlert.error('Something went wrong. Please try again.')
  } finally {
    isToggling = false
  }
}
