export function attachPriceInputListeners(
  minInput: HTMLInputElement | null,
  maxInput: HTMLInputElement | null,
  onMinChange: () => void,
  onMaxChange: () => void,
): void {
  if (!minInput || !maxInput) return

  minInput.addEventListener('input', onMinChange)
  maxInput.addEventListener('input', onMaxChange)
}
