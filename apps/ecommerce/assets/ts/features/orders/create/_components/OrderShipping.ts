import type { HtmlInputList } from '@/ts/shared/types'

import { handleShippingToggle } from '@/ts/features/orders/create/_handlers/orderShippingHandler'
import { attachOrderShippingChangeListener } from '@/ts/features/orders/create/_listeners/orderShippingListener'

export default class OrderShipping {
  private readonly checkbox: HTMLInputElement
  private readonly shippingData: HTMLElement
  private readonly inputs: HtmlInputList

  constructor(checkboxSelector: string, shippingDataSelector: string) {
    const checkbox = document.getElementById(checkboxSelector)
    const shippingData = document.getElementById(shippingDataSelector)

    if (!(checkbox instanceof HTMLInputElement) || !shippingData) {
      throw new Error('OrderShipping: required elements not found.')
    }

    this.checkbox = checkbox
    this.shippingData = shippingData
    this.inputs = shippingData.querySelectorAll('input')

    this.init()
  }

  private init(): void {
    attachOrderShippingChangeListener(this.checkbox, this.shippingData, this.inputs)
    handleShippingToggle(this.checkbox, this.shippingData, this.inputs)
  }
}
