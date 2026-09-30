import type { CartInstance } from '@/ts/features/cart/types'
import { toggleCart } from '@/ts/features/cart/_ui/toggle'

function makeCart(sidebar: HTMLElement | null = document.createElement('div')): CartInstance {
  return {
    isOpen: false,
    elements: {
      openButton: null,
      sidebar,
      closeButton: null,
      itemCountHeader: null,
      warningBox: null,
      warningCloseButton: null,
      carts: [],
    },
  }
}

describe('toggleCart()', () => {
  it('should set isOpen to true when opening', () => {
    const cart = makeCart()
    toggleCart(cart, true)
    expect(cart.isOpen).toBe(true)
  })

  it('should set isOpen to false when closing', () => {
    const cart = makeCart()
    cart.isOpen = true
    toggleCart(cart, false)
    expect(cart.isOpen).toBe(false)
  })

  it('should add cart--active class to sidebar when opening', () => {
    const sidebar = document.createElement('div')
    const cart = makeCart(sidebar)
    toggleCart(cart, true)
    expect(sidebar.classList.contains('cart--active')).toBe(true)
  })

  it('should remove cart--active class from sidebar when closing', () => {
    const sidebar = document.createElement('div')
    sidebar.classList.add('cart--active')
    const cart = makeCart(sidebar)
    toggleCart(cart, false)
    expect(sidebar.classList.contains('cart--active')).toBe(false)
  })

  it('should not throw when sidebar is null', () => {
    const cart = makeCart(null)
    expect(() => toggleCart(cart, true)).not.toThrow()
  })

  it('should not throw when elements is null', () => {
    const cart: CartInstance = { isOpen: false, elements: null }
    expect(() => toggleCart(cart, true)).not.toThrow()
    expect(cart.isOpen).toBe(true)
  })
})
