import { mockCartUiToggle } from '@/tests/_support/mocks/features/cart.mocks'

mockCartUiToggle()

import type { CartInstance } from '@/ts/features/cart/types'
import { toggleCart } from '@/ts/features/cart/_ui/toggle'
import { attachCartWarningListener } from '@/ts/features/cart/_listeners/warningListener'

const mockedToggleCart = vi.mocked(toggleCart)

function makeCart(warningCloseButton: HTMLElement | null, warningBox: HTMLElement | null = null): CartInstance {
  return {
    isOpen: true,
    elements: {
      openButton: null,
      sidebar: null,
      closeButton: null,
      itemCountHeader: null,
      warningBox,
      warningCloseButton,
      carts: [],
    },
  }
}

describe('attachCartWarningListener()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    vi.useFakeTimers()
  })

  afterEach(() => {
    vi.useRealTimers()
  })

  it('should do nothing when cart has no elements', () => {
    const cart: CartInstance = { isOpen: true, elements: null }
    expect(() => attachCartWarningListener(cart)).not.toThrow()
    expect(mockedToggleCart).not.toHaveBeenCalled()
  })

  it('should do nothing when warningCloseButton is null', () => {
    const cart = makeCart(null)
    expect(() => attachCartWarningListener(cart)).not.toThrow()
    expect(mockedToggleCart).not.toHaveBeenCalled()
  })

  it('should call toggleCart with false when warningCloseButton is clicked', () => {
    const closeBtn = document.createElement('button')
    const cart = makeCart(closeBtn)

    attachCartWarningListener(cart)
    closeBtn.click()

    expect(mockedToggleCart).toHaveBeenCalledWith(cart, false)
  })

  it('should hide warningBox after 500ms timeout', () => {
    const closeBtn = document.createElement('button')
    const warningBox = document.createElement('div')
    warningBox.style.display = 'block'
    const cart = makeCart(closeBtn, warningBox)

    attachCartWarningListener(cart)
    closeBtn.click()

    expect(warningBox.style.display).toBe('block')

    vi.advanceTimersByTime(500)

    expect(warningBox.style.display).toBe('none')
  })

  it('should not hide warningBox before 500ms timeout', () => {
    const closeBtn = document.createElement('button')
    const warningBox = document.createElement('div')
    warningBox.style.display = 'block'
    const cart = makeCart(closeBtn, warningBox)

    attachCartWarningListener(cart)
    closeBtn.click()

    vi.advanceTimersByTime(499)

    expect(warningBox.style.display).toBe('block')
  })

  it('should not throw when warningBox is null after timeout', () => {
    const closeBtn = document.createElement('button')
    const cart = makeCart(closeBtn, null)

    attachCartWarningListener(cart)
    closeBtn.click()

    expect(() => vi.advanceTimersByTime(500)).not.toThrow()
  })
})
