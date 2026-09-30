import type { BaseModalInstance } from '@/ts/shared/elements/modal/types'

export function attachKeydownListener(instance: BaseModalInstance, onClose: () => void): void {
  instance.keydownHandler = (e: KeyboardEvent): void => {
    if (e.key === 'Escape') onClose()
  }

  document.addEventListener('keydown', instance.keydownHandler)
}

export function detachKeydownListener(instance: BaseModalInstance): void {
  if (!instance.keydownHandler) return

  document.removeEventListener('keydown', instance.keydownHandler)
  instance.keydownHandler = null
}
