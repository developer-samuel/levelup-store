type PriceOperator = 'min' | 'max'

function clampPrice(primary: HTMLInputElement, secondary: HTMLInputElement, operator: PriceOperator): void {
  const primaryVal = parseInt(primary.value, 10)
  const secondaryVal = parseInt(secondary.value, 10)

  if (operator === 'min' ? primaryVal > secondaryVal : primaryVal < secondaryVal) {
    secondary.value = primary.value
  }
}

export function clampMinPrice(minInput: HTMLInputElement, maxInput: HTMLInputElement): void {
  clampPrice(minInput, maxInput, 'min')
}

export function clampMaxPrice(minInput: HTMLInputElement, maxInput: HTMLInputElement): void {
  clampPrice(maxInput, minInput, 'max')
}
