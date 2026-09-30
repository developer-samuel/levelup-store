const DEFAULT_MAX_LENGTH = 250

export function updateCounterColor(
  textarea: HTMLTextAreaElement,
  counter: HTMLElement,
  maxLength = DEFAULT_MAX_LENGTH,
): void {
  const length = textarea.value.length

  counter.textContent = `${length}/${maxLength}`
  counter.classList.toggle('text-red', length > maxLength)
}
