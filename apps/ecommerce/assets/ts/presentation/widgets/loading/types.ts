export type LoadingInstance = {
  selector: string
  hiddenClass: string
  delay: number
  element: HTMLElement | null
  cancelHide: (() => void) | null
}
