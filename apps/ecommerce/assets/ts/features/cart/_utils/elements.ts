import { query, queryAll } from '@/ts/shared/utils/dom/query'

import type { CartElements, CartContainer } from '@/ts/features/cart/types'

export function getCartElements(): CartElements {
  const openButton = query<HTMLElement>('#cart-icon')
  const sidebar = query<HTMLElement>('#cart-sidebar')
  const closeButton = query<HTMLElement>('#header-close')
  const itemCountHeader = query<HTMLElement>('#header-cart-count')
  const warningBox = query<HTMLElement>('.cart__warning')
  const warningCloseButton = query<HTMLElement>('.cart__warning-header-close')

  const carts: CartContainer[] = Array.from(queryAll<HTMLElement>('.cart') ?? []).map((container) => ({
    container,
    contentWrapper: container.querySelector<HTMLElement>('.cart__content-wrapper'),
    itemCountDetails: container.querySelector<HTMLElement>('.cart__header-count'),
    summary: container.querySelector<HTMLElement>('.cart__summary'),
    totalPrice: container.querySelector<HTMLElement>('.cart__summary-price-span'),
    alertBox: container.querySelector<HTMLElement>('.alert'),
    alertMessage: container.querySelector<HTMLElement>('.alert__body'),
  }))

  return {
    openButton,
    sidebar,
    closeButton,
    itemCountHeader,
    warningBox,
    warningCloseButton,
    carts,
  }
}
