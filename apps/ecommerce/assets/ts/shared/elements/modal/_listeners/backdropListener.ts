export function attachBackdropListener(modal: HTMLElement, bodySelector: string, onClose: () => void): void {
  modal.addEventListener('click', (e: MouseEvent) => {
    const body = modal.querySelector(bodySelector)

    if (body && !body.contains(e.target as Node)) {
      onClose()
    }
  })
}
