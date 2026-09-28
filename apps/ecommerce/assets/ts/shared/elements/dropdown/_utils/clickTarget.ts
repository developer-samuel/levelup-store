/** Returns true if the click target is inside the toggle or dropdown element */
export function isClickInside(toggleEl: HTMLElement, dropdownEl: HTMLElement, target: EventTarget | null): boolean {
  const node = target instanceof Node ? target : null

  return toggleEl.contains(node) || dropdownEl.contains(node)
}
