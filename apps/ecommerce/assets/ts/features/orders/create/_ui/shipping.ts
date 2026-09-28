import type { HtmlInputList } from '@/ts/shared/types'

/** Updates the visual state of the shipping container */
export function updateShipping(shippingData: HTMLElement, enabled: boolean): void {
  shippingData.style.opacity = enabled ? '1' : '0.5'
  shippingData.style.pointerEvents = enabled ? 'auto' : 'none'
}

/** Enables or disables shipping input fields */
export function toggleShipping(inputs: HtmlInputList | HTMLInputElement[], enabled: boolean): void {
  inputs.forEach((input) => {
    if (enabled) {
      input.removeAttribute('disabled')
    } else {
      input.setAttribute('disabled', 'true')
    }
  })
}
