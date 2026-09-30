import type { ReviewModalInstance } from '@/ts/features/reviews/modal/types'
import { updateCounterColor } from '@/ts/features/reviews/modal/_ui/bodyCounter'

const DEFAULT_MAX_LENGTH = 250

export function attachBodyCounterListener(modalInstance: ReviewModalInstance): void {
  const textarea = modalInstance.modal.querySelector<HTMLTextAreaElement>('#body')
  const counter = modalInstance.modal.querySelector<HTMLElement>('#review-body-chars')

  if (!textarea || !counter) return

  textarea.addEventListener('input', () => updateCounterColor(textarea, counter, DEFAULT_MAX_LENGTH))
}
