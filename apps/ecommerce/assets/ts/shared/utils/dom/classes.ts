export function toggleClass(element: HTMLElement | null, className: string, condition: boolean): void {
  if (!element?.classList) return

  if (condition) {
    element.classList.add(className)
  } else {
    element.classList.remove(className)
  }
}
