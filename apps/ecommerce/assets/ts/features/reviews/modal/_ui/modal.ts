import type { ReviewModalInstance } from '@/ts/features/reviews/modal/types'
import { TEXTS } from '@/ts/features/reviews/modal/constants'

export function resetModal(modalInstance: ReviewModalInstance): void {
  const { title, fields, justRate, writeReview, actionBtn, visibleClass } = modalInstance

  if (title) title.textContent = TEXTS.TITLE_RATE

  fields?.classList.remove(visibleClass)
  justRate?.classList.remove(visibleClass)
  writeReview?.classList.add(visibleClass)

  if (actionBtn) actionBtn.textContent = TEXTS.CTA_RATE
}
