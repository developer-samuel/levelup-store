export function setCursorPointer(elements: Iterable<HTMLElement>): void {
  for (const el of elements) {
    el.style.cursor = 'pointer'
  }
}
