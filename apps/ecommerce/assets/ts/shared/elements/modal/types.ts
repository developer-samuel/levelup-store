export type BaseModalInstance = {
  modal: HTMLElement
  visibleClass: string
  keydownHandler: ((e: KeyboardEvent) => void) | null
}
