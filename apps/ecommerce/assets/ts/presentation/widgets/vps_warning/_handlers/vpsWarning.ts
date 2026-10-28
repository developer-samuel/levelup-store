export const bindVpsWarning = (trigger: HTMLElement, modal: HTMLElement): void => {
  const closeBtn = modal.querySelector<HTMLButtonElement>('.vps-warning__modal-close')

  const open = (): void => {
    modal.hidden = false
    modal.classList.add('visible')
  }

  const close = (): void => {
    modal.hidden = true
    modal.classList.remove('visible')
  }

  trigger.addEventListener('click', open)
  closeBtn?.addEventListener('click', close)
  modal.addEventListener('click', (e) => {
    if (e.target === modal) close()
  })
}
