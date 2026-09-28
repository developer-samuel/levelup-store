import type { CartElements, CartInstance } from '@/ts/features/cart/types'
import { getCartElements } from '@/ts/features/cart/_utils/elements'
import {
  attachCartToggleListeners,
  attachCartResizeListener,
  attachCartDocumentClickListener,
} from '@/ts/features/cart/_listeners/domListener'
import { attachCartWarningListener } from '@/ts/features/cart/_listeners/warningListener'

export default class Cart implements CartInstance {
  elements: CartElements | null
  isOpen: boolean

  constructor() {
    this.elements = getCartElements()
    this.isOpen = false

    this.initListeners()
  }

  private initListeners(): void {
    attachCartToggleListeners(this)
    attachCartWarningListener(this)
    attachCartResizeListener(this)
    attachCartDocumentClickListener(this)
  }
}
