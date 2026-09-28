import type { ReviewModalInstance } from '@/ts/features/reviews/modal/types'
import { TEXTS } from '@/ts/features/reviews/modal/constants'

export function toggleReviewMode(modalInstance: ReviewModalInstance, forceReview = true): void {
  modalInstance.isReviewMode = forceReview

  const { title, fields, justRate, writeReview, actionBtn, visibleClass } = modalInstance

  if (forceReview) {
    if (title) title.textContent = TEXTS.TITLE_REVIEW
    if (actionBtn) actionBtn.textContent = TEXTS.CTA_REVIEW

    fields?.classList.add(visibleClass)
    justRate?.classList.add(visibleClass)
    writeReview?.classList.remove(visibleClass)
  } else {
    if (title) title.textContent = TEXTS.TITLE_RATE
    if (actionBtn) actionBtn.textContent = TEXTS.CTA_RATE

    fields?.classList.remove(visibleClass)
    justRate?.classList.remove(visibleClass)
    writeReview?.classList.add(visibleClass)
  }
}
