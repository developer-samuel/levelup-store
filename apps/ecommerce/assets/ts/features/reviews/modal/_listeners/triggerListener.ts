type TriggerInput = HTMLElement | HTMLElement[] | NodeList | HTMLCollection | null

export function attachTriggerListener(triggers: TriggerInput, toggle: (el: HTMLElement) => void): void {
  if (!triggers) return

  const list: HTMLElement[] =
    triggers instanceof NodeList || triggers instanceof HTMLCollection || Array.isArray(triggers)
      ? Array.from(triggers).filter((t): t is HTMLElement => t instanceof HTMLElement)
      : [triggers]

  list.forEach((t) => {
    t.addEventListener('click', (e: MouseEvent) => {
      if (e.currentTarget instanceof HTMLElement) toggle(e.currentTarget)
    })
  })
}
