function setPriceOutput(output: HTMLElement | null, value: string): void {
  if (output) output.textContent = value + ' €'
}

export function updatePriceOutputs(
  minInput: HTMLInputElement | null,
  maxInput: HTMLInputElement | null,
  minOutput: HTMLElement | null,
  maxOutput: HTMLElement | null,
): void {
  if (!minInput || !maxInput) return

  setPriceOutput(minOutput, minInput.value || '')
  setPriceOutput(maxOutput, maxInput.value || '')
}
