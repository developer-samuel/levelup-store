import { clampMinPrice, clampMaxPrice } from '@/ts/features/products/list/_utils/price'
import { updatePriceOutputs } from '@/ts/features/products/list/_ui/price'
import { attachPriceInputListeners } from '@/ts/features/products/list/_listeners/priceInputListener'

/**
 * Price range slider component.
 *
 * Keeps min/max inputs in sync with each other and updates the
 * displayed price output labels on every change.
 */
export default class PriceRange {
  private readonly minPrice: HTMLInputElement | null
  private readonly maxPrice: HTMLInputElement | null
  private readonly minPriceOutput: HTMLElement | null
  private readonly maxPriceOutput: HTMLElement | null

  constructor(minInputId: string, maxInputId: string, minOutputId: string, maxOutputId: string) {
    this.minPrice = document.getElementById(minInputId) as HTMLInputElement | null
    this.maxPrice = document.getElementById(maxInputId) as HTMLInputElement | null
    this.minPriceOutput = document.getElementById(minOutputId)
    this.maxPriceOutput = document.getElementById(maxOutputId)

    this.initListeners()
  }

  private initListeners(): void {
    attachPriceInputListeners(
      this.minPrice,
      this.maxPrice,
      () => {
        if (!this.minPrice || !this.maxPrice) return

        clampMinPrice(this.minPrice, this.maxPrice)
        updatePriceOutputs(this.minPrice, this.maxPrice, this.minPriceOutput, this.maxPriceOutput)
      },
      () => {
        if (!this.minPrice || !this.maxPrice) return

        clampMaxPrice(this.minPrice, this.maxPrice)
        updatePriceOutputs(this.minPrice, this.maxPrice, this.minPriceOutput, this.maxPriceOutput)
      },
    )
  }
}
