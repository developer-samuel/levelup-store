import type { HtmlInputList } from '@/ts/shared/types'

import { handleShippingToggle } from '@/ts/features/orders/create/_handlers/orderShippingHandler'

export function attachOrderShippingChangeListener(
  checkbox: HTMLInputElement | null,
  shippingData: HTMLElement,
  inputs: HtmlInputList,
): void {
  if (!checkbox) return
  checkbox.addEventListener('change', () => {
    handleShippingToggle(checkbox, shippingData, inputs)
  })
}
