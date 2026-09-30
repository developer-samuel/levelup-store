import { mockUtilsDebouce } from '@/tests/_support/mocks/shared/utils.mocks'
import { mockCartUiToggle } from '@/tests/_support/mocks/features/cart.mocks'

mockUtilsDebouce()
mockCartUiToggle()

vi.mock('@/ts/features/cart/_handlers/documentClickHandler', () => ({
  handleDocumentClick: vi.fn(),
}))

import type { CartInstance } from '@/ts/features/cart/types'
import { toggleCart } from '@/ts/features/cart/_ui/toggle'
import { handleDocumentClick } from '@/ts/features/cart/_handlers/documentClickHandler'
import {
  attachCartToggleListeners,
  attachCartResizeListener,
  attachCartDocumentClickListener,
} from '@/ts/features/cart/_listeners/domListener'

const mockedToggleCart = vi.mocked(toggleCart)
const mockedHandleDocumentClick = vi.mocked(handleDocumentClick)

function makeCart(overrides: Partial<CartInstance['elements']> = {}): CartInstance {
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
      ...overrides,
    },
  }
}

describe('attachCartToggleListeners()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should do nothing when cart has no elements', () => {
    const cart: CartInstance = { isOpen: false, elements: null }
    expect(() => attachCartToggleListeners(cart)).not.toThrow()
    expect(mockedToggleCart).not.toHaveBeenCalled()
  })

  it('should open cart when openButton is clicked', () => {
    const openButton = document.createElement('button')
    const cart = makeCart({ openButton })

    attachCartToggleListeners(cart)
    openButton.click()

    expect(mockedToggleCart).toHaveBeenCalledWith(cart, true)
  })

  it('should close cart when closeButton is clicked', () => {
    const closeButton = document.createElement('button')
    const cart = makeCart({ closeButton })

    attachCartToggleListeners(cart)
    closeButton.click()

    expect(mockedToggleCart).toHaveBeenCalledWith(cart, false)
  })

  it('should not throw when openButton is null', () => {
    const cart = makeCart({ openButton: null })
    expect(() => attachCartToggleListeners(cart)).not.toThrow()
  })

  it('should not throw when closeButton is null', () => {
    const cart = makeCart({ closeButton: null })
    expect(() => attachCartToggleListeners(cart)).not.toThrow()
  })
})

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

describe('attachCartDocumentClickListener()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should call handleDocumentClick when document is clicked', async () => {
    mockedHandleDocumentClick.mockResolvedValueOnce(undefined)
    const cart = makeCart()

    attachCartDocumentClickListener(cart)
    document.dispatchEvent(new MouseEvent('click', { bubbles: true }))

    await vi.waitFor(() => {
      expect(mockedHandleDocumentClick).toHaveBeenCalledTimes(1)
    })
  })

  it('should pass the cart instance to handleDocumentClick', async () => {
    mockedHandleDocumentClick.mockResolvedValueOnce(undefined)
    const cart = makeCart()

    attachCartDocumentClickListener(cart)
    document.dispatchEvent(new MouseEvent('click', { bubbles: true }))

    await vi.waitFor(() => {
      expect(mockedHandleDocumentClick).toHaveBeenCalledWith(expect.any(MouseEvent), cart)
    })
  })
})
