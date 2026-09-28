export const HEADER_TOGGLE = 'header:toggle'

export function dispatchHeaderToggle(header: HTMLElement, hidden: boolean): void {
  header.dispatchEvent(new CustomEvent(HEADER_TOGGLE, { bubbles: true, detail: { hidden } }))
}
