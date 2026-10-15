import { mockUtilsDebouce } from '@/tests/_support/mocks/shared/utils.mocks'
import { mockCartUiToggle } from '@/tests/_support/mocks/features/cart.mocks'

mockUtilsDebouce()
mockCartUiToggle()

import type { CartInstance } from '@/ts/features/cart/types'
import { toggleCart } from '@/ts/features/cart/_ui/toggle'
import { attachCartResizeListener } from '@/ts/features/cart/_listeners/resizeListener'

const mockedToggleCart = vi.mocked(toggleCart)

function makeCart(): CartInstance {
  return {
    isOpen: false,
    elements: {
      openButton: null,
      sidebar: null,
      closeButton: null,
      itemCountHeader: null,
      warningBox: null,
      warningCloseButton: null,
      carts: [],
    },
  }
}

describe('attachCartResizeListener()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  afterEach(() => {
    vi.restoreAllMocks()
  })

  it('should close cart when resized to >= 1024px and cart is open', () => {
    const cart = makeCart()
    cart.isOpen = true

    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(1024)

    attachCartResizeListener(cart)
    window.dispatchEvent(new Event('resize'))

    expect(mockedToggleCart).toHaveBeenCalledWith(cart, false)
  })

  it('should not close cart when resized to < 1024px', () => {
    const cart = makeCart()
    cart.isOpen = true

    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(768)

    attachCartResizeListener(cart)
    window.dispatchEvent(new Event('resize'))

    expect(mockedToggleCart).not.toHaveBeenCalled()
  })

  it('should not close cart when width >= 1024px but cart is closed', () => {
    const cart = makeCart()
    cart.isOpen = false

    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(1280)

    attachCartResizeListener(cart)
    window.dispatchEvent(new Event('resize'))

    expect(mockedToggleCart).not.toHaveBeenCalledWith(cart, false)
  })
})
