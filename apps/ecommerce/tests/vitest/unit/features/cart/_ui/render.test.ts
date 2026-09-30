vi.mock('@/ts/features/cart/_utils/elements', () => ({
  getCartElements: vi.fn(),
}))

vi.mock('@/ts/features/cart/_ui/price', () => ({
  updateTotalPrice: vi.fn(),
}))

vi.mock('@/ts/features/cart/_ui/summary', () => ({
  updateSummaryVisibility: vi.fn(),
}))

import type { CartContainer, CartElements } from '@/ts/features/cart/types'
import { getCartElements } from '@/ts/features/cart/_utils/elements'
import { updateTotalPrice } from '@/ts/features/cart/_ui/price'
import { updateSummaryVisibility } from '@/ts/features/cart/_ui/summary'
import { renderCart } from '@/ts/features/cart/_ui/render'

const mockedGetCartElements = vi.mocked(getCartElements)
const mockedUpdateTotalPrice = vi.mocked(updateTotalPrice)
const mockedUpdateSummaryVisibility = vi.mocked(updateSummaryVisibility)

function makeCartContainer(overrides: Partial<CartContainer> = {}): CartContainer {
  return {
    container: document.createElement('div'),
    contentWrapper: null,
    itemCountDetails: null,
    summary: null,
    totalPrice: null,
    alertBox: null,
    alertMessage: null,
    ...overrides,
  }
}

function makeElements(carts: CartContainer[] = [], itemCountHeader: HTMLElement | null = null): CartElements {
  return {
    openButton: null,
    sidebar: null,
    closeButton: null,
    itemCountHeader,
    warningBox: null,
    warningCloseButton: null,
    carts,
  }
}

describe('renderCart()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should update itemCountHeader text for totalItems < 10', () => {
    const itemCountHeader = document.createElement('span')
    mockedGetCartElements.mockReturnValueOnce(makeElements([], itemCountHeader))

    renderCart({ totalItems: 3 })

    expect(itemCountHeader.textContent).toBe('3')
  })

  it('should set itemCountHeader to "9+" when totalItems >= 10', () => {
    const itemCountHeader = document.createElement('span')
    mockedGetCartElements.mockReturnValueOnce(makeElements([], itemCountHeader))

    renderCart({ totalItems: 10 })

    expect(itemCountHeader.textContent).toBe('9+')
  })

  it('should set itemCountHeader to "0" when totalItems is undefined', () => {
    const itemCountHeader = document.createElement('span')
    mockedGetCartElements.mockReturnValueOnce(makeElements([], itemCountHeader))

    renderCart({})

    expect(itemCountHeader.textContent).toBe('0')
  })

  it('should not throw when itemCountHeader is null', () => {
    mockedGetCartElements.mockReturnValueOnce(makeElements([], null))
    expect(() => renderCart({ totalItems: 5 })).not.toThrow()
  })

  it('should update itemCountDetails text for each cart', () => {
    const itemCountDetails = document.createElement('span')
    const cart = makeCartContainer({ itemCountDetails })
    mockedGetCartElements.mockReturnValueOnce(makeElements([cart]))

    renderCart({ totalItems: 4 })

    expect(itemCountDetails.textContent).toBe('Items: 4')
  })

  it('should set itemCountDetails to "Items: 0" when totalItems is undefined', () => {
    const itemCountDetails = document.createElement('span')
    const cart = makeCartContainer({ itemCountDetails })

    mockedGetCartElements.mockReturnValueOnce(makeElements([cart]))

    renderCart({})

    expect(itemCountDetails.textContent).toBe('Items: 0')
  })

  it('should inject html into contentWrapper when html is provided', () => {
    const contentWrapper = document.createElement('div')
    const container = document.createElement('div')
    const cart = makeCartContainer({ contentWrapper, container })
    mockedGetCartElements.mockReturnValueOnce(makeElements([cart]))

    renderCart({ html: '<p>Item</p>' })

    expect(contentWrapper.innerHTML).toBe('<p>Item</p>')
  })

  it('should not inject html when data.html is undefined even if contentWrapper exists', () => {
    const contentWrapper = document.createElement('div')
    contentWrapper.innerHTML = 'original'

    const cart = makeCartContainer({ contentWrapper })

    mockedGetCartElements.mockReturnValueOnce(makeElements([cart]))

    renderCart({})

    expect(contentWrapper.innerHTML).toBe('original')
  })

  it('should not inject html when contentWrapper is null', () => {
    const cart = makeCartContainer({ contentWrapper: null })
    mockedGetCartElements.mockReturnValueOnce(makeElements([cart]))
    expect(() => renderCart({ html: '<p>Item</p>' })).not.toThrow()
  })

  it('should call updateTotalPrice for each cart', () => {
    const cart = makeCartContainer()
    const data = { totalItems: 2 }
    mockedGetCartElements.mockReturnValueOnce(makeElements([cart]))

    renderCart(data)

    expect(mockedUpdateTotalPrice).toHaveBeenCalledWith(cart, data)
  })

  it('should call updateSummaryVisibility for each cart', () => {
    const cart = makeCartContainer()
    const data = { totalItems: 2 }
    mockedGetCartElements.mockReturnValueOnce(makeElements([cart]))

    renderCart(data)

    expect(mockedUpdateSummaryVisibility).toHaveBeenCalledWith(data, cart)
  })

  it('should re-query summary and totalPrice elements after html inject', () => {
    const container = document.createElement('div')
    const summary = document.createElement('div')
    summary.className = 'cart__summary'
    const priceSpan = document.createElement('span')
    priceSpan.className = 'cart__summary-price-span'
    container.appendChild(summary)
    container.appendChild(priceSpan)

    const contentWrapper = document.createElement('div')
    const cart = makeCartContainer({ container, contentWrapper })
    mockedGetCartElements.mockReturnValueOnce(makeElements([cart]))

    renderCart({ html: '<p>new</p>' })

    expect(cart.summary).toBe(container.querySelector('.cart__summary'))
    expect(cart.totalPrice).toBe(container.querySelector('.cart__summary-price-span'))
  })
})
