import type { ZoomModalInstance } from '@/ts/features/products/detail/zoom/types'
import { getZoomModalElements } from '@/ts/features/products/detail/zoom/_ui/elements'
import {
  attachZoomOpenListener,
  attachZoomCloseListeners,
} from '@/ts/features/products/detail/zoom/_listeners/zoomListener'

const VISIBLE_CLASS = 'visible'

export default class ZoomModal implements ZoomModalInstance {
  modal: HTMLElement
  img: HTMLImageElement
  close: HTMLElement
  visibleClass: string
  keydownHandler: ((e: KeyboardEvent) => void) | null = null

  constructor(trigger: HTMLElement, getCurrentSrc: () => string) {
    const { modal, img, close } = getZoomModalElements()

    if (!modal || !img || !close) throw new Error('ZoomModal: required elements not found')

    this.modal = modal
    this.img = img
    this.close = close
    this.visibleClass = VISIBLE_CLASS

    attachZoomOpenListener(trigger, this, getCurrentSrc)
    attachZoomCloseListeners(this)
  }
}
