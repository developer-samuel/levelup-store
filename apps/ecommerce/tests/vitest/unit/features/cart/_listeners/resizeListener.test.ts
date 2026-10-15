import { mockCartUiToggle } from '@/tests/_support/mocks/features/cart.mocks'

mockCartUiToggle()

import type { CartInstance } from '@/ts/features/cart/types'
import { toggleCart } from '@/ts/features/cart/_ui/toggle'
import { attachCartResizeListener } from '@/ts/features/cart/_listeners/resizeListener'

const mockedToggleCart = vi.mocked(toggleCart)

function makeCart(isOpen = false): CartInstance {
  return { isOpen, elements: null }
}

function setViewport(width: number): void {
  Object.defineProperty(window, 'innerWidth', { writable: true, configurable: true, value: width })
}

beforeEach(() => {
  vi.useFakeTimers()
  mockedToggleCart.mockClear()
})

afterEach(() => {
  vi.useRealTimers()
})

describe('attachCartResizeListener()', () => {
  it('should close cart when resizing to LG and cart is open', () => {
    setViewport(1023)
    const cart = makeCart(true)
    attachCartResizeListener(cart)

    setViewport(1024)
    window.dispatchEvent(new Event('resize'))
    vi.advanceTimersByTime(200)

    expect(mockedToggleCart).toHaveBeenCalledWith(cart, false)
  })

  it('should not close cart when resizing below LG', () => {
    setViewport(1024)
    const cart = makeCart(true)
    attachCartResizeListener(cart)

    setViewport(800)
    window.dispatchEvent(new Event('resize'))
    vi.advanceTimersByTime(200)

    expect(mockedToggleCart).not.toHaveBeenCalledWith(cart, false)
  })

  it('should not close cart when already closed and resizing to LG', () => {
    setViewport(1023)
    const cart = makeCart(false)
    attachCartResizeListener(cart)

    setViewport(1024)
    window.dispatchEvent(new Event('resize'))
    vi.advanceTimersByTime(200)

    expect(mockedToggleCart).not.toHaveBeenCalledWith(cart, false)
  })
})
