function getFocusableElements(container: HTMLElement, selector: string): HTMLElement[] {
  return Array.from(container.querySelectorAll<HTMLElement>(selector))
}

export function trapTab(e: KeyboardEvent, modal: HTMLElement, focusableSelector: string): void {
  const focusables = getFocusableElements(modal, focusableSelector)

  if (focusables.length === 0) {
    e.preventDefault()
    return
  }

  const first = focusables[0]
  const last = focusables[focusables.length - 1]

  if (!first || !last) return

  if (e.shiftKey && document.activeElement === first) {
    e.preventDefault()
    last.focus()
  } else if (!e.shiftKey && document.activeElement === last) {
    e.preventDefault()
    first.focus()
  }
}

export function focusFirst(modal: HTMLElement, focusableSelector: string): void {
  const first = getFocusableElements(modal, focusableSelector)[0]

  if (first) first.focus()
  else modal.focus()
}
