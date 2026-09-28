export type CartContainer = {
  container: HTMLElement
  contentWrapper: HTMLElement | null
  itemCountDetails: HTMLElement | null
  summary: HTMLElement | null
  totalPrice: HTMLElement | null
  alertBox: HTMLElement | null
  alertMessage: HTMLElement | null
}

export type CartElements = {
  openButton: HTMLElement | null
  sidebar: HTMLElement | null
  closeButton: HTMLElement | null
  itemCountHeader: HTMLElement | null
  warningBox: HTMLElement | null
  warningCloseButton: HTMLElement | null
  carts: CartContainer[]
}

export type CartResponse = {
  success?: boolean
  message?: string
  html?: string
  totalItems?: number
  totalPrice?: number | string
  [key: string]: unknown
}

export type CartAction = 'add' | 'remove'

export type CartInstance = {
  elements: CartElements | null
  isOpen: boolean
}
