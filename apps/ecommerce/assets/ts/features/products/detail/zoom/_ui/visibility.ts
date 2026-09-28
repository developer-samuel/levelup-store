import { show, hide } from '@/ts/shared/elements/modal/_ui/visibility'

import type { ZoomModalInstance } from '@/ts/features/products/detail/zoom/types'

export function showZoomModal(instance: ZoomModalInstance, src: string): void {
  instance.img.src = src
  show(instance.modal, instance.visibleClass)
  document.body.style.overflow = 'hidden'
}

export function hideZoomModal(instance: ZoomModalInstance): void {
  hide(instance.modal, instance.visibleClass)
  document.body.style.overflow = ''
}
