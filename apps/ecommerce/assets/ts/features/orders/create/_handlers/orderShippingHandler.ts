import type { HtmlInputList } from '@/ts/shared/types'

import { updateShipping, toggleShipping } from '@/ts/features/orders/create/_ui/shipping'

/** Toggles shipping fields and updates UI based on checkbox state */
export function handleShippingToggle(
  checkbox: HTMLInputElement,
  shippingData: HTMLElement,
  inputs: HtmlInputList,
): void {
  const enabled = checkbox.checked

  updateShipping(shippingData, enabled)
  toggleShipping(inputs, enabled)
}
