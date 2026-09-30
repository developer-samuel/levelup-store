import type { ReviewModalInstance } from '@/ts/features/reviews/modal/types'
import { handleKeydown } from '@/ts/features/reviews/modal/_handlers/keydownHandler'

export function attachKeydownListener(instance: ReviewModalInstance, onClose: () => void): void {
  instance.keydownHandler = (e: KeyboardEvent): void =>
    handleKeydown(e, instance.modal, onClose, instance.focusableSelector)

  document.addEventListener('keydown', instance.keydownHandler)
}

export function detachKeydownListener(instance: ReviewModalInstance): void {
  if (!instance.keydownHandler) return

  document.removeEventListener('keydown', instance.keydownHandler)
  instance.keydownHandler = null
}
