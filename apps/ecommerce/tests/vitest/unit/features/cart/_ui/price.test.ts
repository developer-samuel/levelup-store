import type { CartContainer, CartResponse } from '@/ts/features/cart/types'
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

  it('should display integer price without decimals', () => {
    const cart = makeCart()
    updateTotalPrice(cart, { totalPrice: 20 })
    expect(cart.totalPrice?.textContent).toBe('20 €')
  })

  it('should display float price with 2 decimals', () => {
    const cart = makeCart()
    updateTotalPrice(cart, { totalPrice: 19.9 })
    expect(cart.totalPrice?.textContent).toBe('19.90 €')
  })

  it('should parse string price', () => {
    const cart = makeCart()
    updateTotalPrice(cart, { totalPrice: '14.50' })
    expect(cart.totalPrice?.textContent).toBe('14.50 €')
  })

  it('should display 0 € when totalPrice is undefined', () => {
    const cart = makeCart()
    updateTotalPrice(cart, {})
    expect(cart.totalPrice?.textContent).toBe('0 €')
  })

  it('should display 0 € when totalPrice is unparseable string', () => {
    const cart = makeCart()
    updateTotalPrice(cart, { totalPrice: 'abc' } as CartResponse)
    expect(cart.totalPrice?.textContent).toBe('0 €')
  })

  it('should display integer string price without decimals', () => {
    const cart = makeCart()
    updateTotalPrice(cart, { totalPrice: '5' })
    expect(cart.totalPrice?.textContent).toBe('5 €')
  })
})
