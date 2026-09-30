export type ReviewModalInstance = {
  modal: HTMLElement
  visibleClass: string
  focusableSelector: string
  lastActiveElement: HTMLElement | Element | null
  keydownHandler: ((e: KeyboardEvent) => void) | null
  title: HTMLElement | null
  fields: HTMLElement | null
  justRate: HTMLElement | null
  writeReview: HTMLElement | null
  actionBtn: HTMLElement | null
  isReviewMode: boolean
  ratingContainer: HTMLElement | null
  ratingValue: number
}

export type ReviewModalOptions = {
  triggerSelector?: string
  modalSelector?: string
  visibleClass?: string
}

export type RatingState = {
  value: number
}

export type DynamicField = {
  wrapper: HTMLDivElement
  input: HTMLInputElement
  counter: HTMLDivElement
}
