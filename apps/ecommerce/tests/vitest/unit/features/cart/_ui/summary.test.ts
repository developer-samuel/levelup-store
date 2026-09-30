import type { CartContainer } from '@/ts/features/cart/types'
import { updateSummaryVisibility } from '@/ts/features/cart/_ui/summary'

function makeCart(summaryEl: HTMLElement | null = null): CartContainer {
  const container = document.createElement('div')
  if (summaryEl) container.appendChild(summaryEl)
  return {
    container,
    contentWrapper: null,
    itemCountDetails: null,
    summary: summaryEl,
    totalPrice: null,
    alertBox: null,
    alertMessage: null,
  }
}

describe('updateSummaryVisibility()', () => {
  it('should do nothing when no summary element exists', () => {
    const cart = makeCart(null)
    expect(() => updateSummaryVisibility({ totalItems: 2 }, cart)).not.toThrow()
  })

  it('should add visible class when totalItems > 0', () => {
    const summary = document.createElement('div')
    const cart = makeCart(summary)
    updateSummaryVisibility({ totalItems: 3 }, cart)
    expect(summary.classList.contains('visible')).toBe(true)
  })

  it('should remove visible class when totalItems is 0', () => {
    const summary = document.createElement('div')
    summary.classList.add('visible')
    const cart = makeCart(summary)
    updateSummaryVisibility({ totalItems: 0 }, cart)
    expect(summary.classList.contains('visible')).toBe(false)
  })

  it('should remove visible class when totalItems is undefined', () => {
    const summary = document.createElement('div')
    summary.classList.add('visible')
    const cart = makeCart(summary)
    updateSummaryVisibility({}, cart)
    expect(summary.classList.contains('visible')).toBe(false)
  })

  it('should find summary via container querySelector when cart.summary is null', () => {
    const container = document.createElement('div')
    const summary = document.createElement('div')
    summary.className = 'cart__summary'
    container.appendChild(summary)
    const cart: CartContainer = {
      container,
      contentWrapper: null,
      itemCountDetails: null,
      summary: null,
      totalPrice: null,
      alertBox: null,
      alertMessage: null,
    }

    updateSummaryVisibility({ totalItems: 1 }, cart)

    expect(summary.classList.contains('visible')).toBe(true)
  })
})
