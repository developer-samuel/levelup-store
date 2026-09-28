import { updateStars } from '@/ts/features/reviews/modal/_ui/stars'
import {
  attachMouseEnterListener,
  attachMouseLeaveListener,
} from '@/ts/features/reviews/modal/_listeners/mouseListener'
import { attachClickListener } from '@/ts/features/reviews/modal/_listeners/clickListener'
import type { ReviewModalInstance, RatingState } from '@/ts/features/reviews/modal/types'

function bindStars(container: HTMLElement, hiddenValueInput: HTMLInputElement | null, initialValue = 0): void {
  if (!container) return

  const stars = Array.from(container.querySelectorAll<HTMLImageElement>('img'))
  const ratingState: RatingState = { value: initialValue }

  stars.forEach((star, i) => {
    star.dataset.index = String(i + 1)
    star.src = '/img/icons/elements/star/star-empty.png'
    star.classList.remove('active', 'hovered')

    attachMouseEnterListener(star, stars)
    attachMouseLeaveListener(star, stars)
    attachClickListener(star, stars, i + 1, container, updateStars, ratingState, hiddenValueInput)
  })

  if (hiddenValueInput) {
    hiddenValueInput.value = initialValue ? String(initialValue) : ''
  }

  updateStars(container, initialValue)
}

export function setupStars(modalInstance: ReviewModalInstance): void {
  if (!modalInstance.ratingContainer) return

  const hiddenValueInput = modalInstance.modal.querySelector<HTMLInputElement>('#review-value')

  bindStars(modalInstance.ratingContainer, hiddenValueInput, modalInstance.ratingValue)
}
