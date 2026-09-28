const MAX_FIELDS = 5

/** Handles automatic adding and pruning of dynamic review input fields */
export function handleDynamicFieldChange(
  input: HTMLInputElement,
  wrapper: HTMLElement,
  container: HTMLElement,
  addFieldCallback: () => void,
): void {
  if (input.value.length > 0 && wrapper === container.lastElementChild && container.children.length < MAX_FIELDS) {
    addFieldCallback()
  }

  if (container.children.length > 1) {
    const lastChild = container.lastElementChild
    const prevChild = container.children[container.children.length - 2]
    const last = lastChild?.querySelector<HTMLInputElement>('input')
    const prev = prevChild?.querySelector<HTMLInputElement>('input')

    if (lastChild && last && prev && last.value.length === 0 && prev.value.length === 0) {
      lastChild.remove()
    }
  }
}
