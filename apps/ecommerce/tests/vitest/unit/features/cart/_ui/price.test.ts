import type { CartContainer } from '@/ts/features/cart/types'
import { updateTotalPrice } from '@/ts/features/cart/_ui/price'

function makeCart(totalPriceEl: HTMLElement | null = document.createElement('span')): CartContainer {
  return {
    container: document.createElement('div'),
    contentWrapper: null,
    itemCountDetails: null,
    summary: null,
    totalPrice: totalPriceEl,
    alertBox: null,
    alertMessage: null,
  }
}

describe('updateTotalPrice()', () => {
  it('should do nothing when totalPrice element is null', () => {
    const cart = makeCart(null)
    expect(() => updateTotalPrice(cart, { totalPrice: 10 })).not.toThrow()
  })

  it.each([
    [20, '20 €'],
    [19.9, '19.90 €'],
    ['14.50', '14.50 €'],
    [undefined, '0 €'],
    ['abc', '0 €'],
    ['5', '5 €'],
  ] as [number | string | undefined, string][])('should display totalPrice=%s as %s', (input, expected) => {
    const cart = makeCart()
    updateTotalPrice(cart, { totalPrice: input as number })
    expect(cart.totalPrice?.textContent).toBe(expected)
  })
})
