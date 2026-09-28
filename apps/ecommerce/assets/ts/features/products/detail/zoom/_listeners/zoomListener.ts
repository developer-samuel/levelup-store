import { attachKeydownListener, detachKeydownListener } from '@/ts/shared/elements/modal/_listeners/keydownListener'
import { attachBackdropListener } from '@/ts/shared/elements/modal/_listeners/backdropListener'

import type { ZoomModalInstance } from '@/ts/features/products/detail/zoom/types'
import { showZoomModal, hideZoomModal } from '@/ts/features/products/detail/zoom/_ui/visibility'

export function attachZoomOpenListener(
  trigger: HTMLElement,
  instance: ZoomModalInstance,
  getCurrentSrc: () => string,
): void {
  trigger.addEventListener('click', () => {
    showZoomModal(instance, getCurrentSrc())
    attachKeydownListener(instance, () => hideZoomModal(instance))
  })
}

export function attachZoomCloseListeners(instance: ZoomModalInstance): void {
  const close = (): void => {
    hideZoomModal(instance)
    detachKeydownListener(instance)
  }

  instance.close.addEventListener('click', close)
  attachBackdropListener(instance.modal, '.modal-zoom__body', close)
}
