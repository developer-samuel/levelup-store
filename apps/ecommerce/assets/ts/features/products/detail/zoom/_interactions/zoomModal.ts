import { getZoomModalElements } from '@/ts/features/products/detail/zoom/_ui/elements'
import { showZoomModal, hideZoomModal } from '@/ts/features/products/detail/zoom/_ui/visibility'
import { attachKeydownListener, detachKeydownListener } from '@/ts/shared/elements/modal/_listeners/keydownListener'
import { attachBackdropListener } from '@/ts/shared/elements/modal/_listeners/backdropListener'

export function bindZoomModal(): void {
  const { modal, img, close } = getZoomModalElements()

  if (!modal || !img || !close) return

  const instance = { modal, img, close, visibleClass: 'visible', keydownHandler: null as ((e: KeyboardEvent) => void) | null }

  const hide = (): void => {
    hideZoomModal(instance)
    detachKeydownListener(instance)
  }

  close.addEventListener('click', hide)
  attachBackdropListener(modal, '.modal-zoom__body', hide)

  window.openZoomModal = (src: string): void => {
    showZoomModal(instance, src)
    attachKeydownListener(instance, hide)
  }
}
