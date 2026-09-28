import { show, hide } from '@/ts/shared/elements/modal/_ui/visibility'

import type { ReviewModalInstance } from '@/ts/features/reviews/modal/types'
import { focusFirst } from '@/ts/features/reviews/modal/_ui/focus'
import { attachKeydownListener, detachKeydownListener } from '@/ts/features/reviews/modal/_listeners/keydownListener'

function openModal(modalInstance: ReviewModalInstance, triggerEl: HTMLElement | null = null): void {
  modalInstance.lastActiveElement = triggerEl ?? document.activeElement

  show(modalInstance.modal, modalInstance.visibleClass)
  focusFirst(modalInstance.modal, modalInstance.focusableSelector)
  attachKeydownListener(modalInstance, () => closeModal(modalInstance))
}

export function closeModal(modalInstance: ReviewModalInstance): void {
  hide(modalInstance.modal, modalInstance.visibleClass)
  detachKeydownListener(modalInstance)

  const last = modalInstance.lastActiveElement
  if (last instanceof HTMLElement) last.focus()
}

export function toggleModal(modalInstance: ReviewModalInstance, triggerEl: HTMLElement | null = null): void {
  if (modalInstance.modal.classList.contains(modalInstance.visibleClass)) {
    closeModal(modalInstance)
  } else {
    openModal(modalInstance, triggerEl)
  }
}
