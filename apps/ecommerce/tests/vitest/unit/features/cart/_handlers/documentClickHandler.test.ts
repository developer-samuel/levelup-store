import { mockCartUiToggle } from '@/tests/_support/mocks/features/cart.mocks'

mockCartUiToggle()

vi.mock('@/ts/features/cart/_handlers/buyHandler', () => ({
  handleBuy: vi.fn(),
}))

vi.mock('@/ts/features/cart/_handlers/removeCartHandler', () => ({
  handleRemove: vi.fn(),
}))

import { makeMouseEvent } from '@/tests/_support/fakers/events.fakers'

import type { CartInstance } from '@/ts/features/cart/types'
import { toggleCart } from '@/ts/features/cart/_ui/toggle'
import { handleBuy } from '@/ts/features/cart/_handlers/buyHandler'
import { handleRemove } from '@/ts/features/cart/_handlers/removeCartHandler'
import { handleDocumentClick } from '@/ts/features/cart/_handlers/documentClickHandler'

const mockedHandleBuy = vi.mocked(handleBuy)
const mockedHandleRemove = vi.mocked(handleRemove)
const mockedToggleCart = vi.mocked(toggleCart)

function makeCart(sidebar: HTMLElement | null = null, openButton: HTMLElement | null = null): CartInstance {
  return {
    isOpen: true,
    elements: {
      openButton,
      sidebar,
      closeButton: null,
      itemCountHeader: null,
      warningBox: null,
      warningCloseButton: null,
      carts: [],
    },
  }
}

describe('handleDocumentClick()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    document.body.innerHTML = ''
  })

  it('should call handleBuy and stop if it returns true', async () => {
    mockedHandleBuy.mockReturnValueOnce(true)
    const cart = makeCart()

    await handleDocumentClick(makeMouseEvent(document.createElement('div')), cart)

    expect(mockedHandleBuy).toHaveBeenCalledTimes(1)
    expect(mockedHandleRemove).not.toHaveBeenCalled()
    expect(mockedToggleCart).not.toHaveBeenCalled()
  })

  it('should call handleRemove if handleBuy returns false', async () => {
    mockedHandleBuy.mockReturnValueOnce(false)
    mockedHandleRemove.mockResolvedValueOnce(true)

    await handleDocumentClick(makeMouseEvent(document.createElement('div')), makeCart())

    expect(mockedHandleRemove).toHaveBeenCalledTimes(1)
    expect(mockedToggleCart).not.toHaveBeenCalled()
  })

  it('should not call toggleCart if handleRemove returns true', async () => {
    mockedHandleBuy.mockReturnValueOnce(false)
    mockedHandleRemove.mockResolvedValueOnce(true)

    await handleDocumentClick(makeMouseEvent(document.createElement('div')), makeCart())

    expect(mockedToggleCart).not.toHaveBeenCalled()
  })

  it('should close cart when clicking outside sidebar and openButton', async () => {
    mockedHandleBuy.mockReturnValueOnce(false)
    mockedHandleRemove.mockResolvedValueOnce(false)

    const sidebar = document.createElement('div')
    const openButton = document.createElement('button')
    const outsideTarget = document.createElement('span')
    document.body.appendChild(sidebar)
    document.body.appendChild(openButton)
    document.body.appendChild(outsideTarget)

    const cart = makeCart(sidebar, openButton)

    await handleDocumentClick(makeMouseEvent(outsideTarget), cart)

    expect(mockedToggleCart).toHaveBeenCalledWith(cart, false)
  })

  it('should not close cart when clicking inside sidebar', async () => {
    mockedHandleBuy.mockReturnValueOnce(false)
    mockedHandleRemove.mockResolvedValueOnce(false)

    const sidebar = document.createElement('div')
    const inner = document.createElement('span')
    sidebar.appendChild(inner)
    document.body.appendChild(sidebar)

    const cart = makeCart(sidebar, document.createElement('button'))

    await handleDocumentClick(makeMouseEvent(inner), cart)

    expect(mockedToggleCart).not.toHaveBeenCalled()
  })

  it('should not close cart when clicking the openButton', async () => {
    mockedHandleBuy.mockReturnValueOnce(false)
    mockedHandleRemove.mockResolvedValueOnce(false)

    const sidebar = document.createElement('div')
    const openButton = document.createElement('button')
    document.body.appendChild(sidebar)
    document.body.appendChild(openButton)

    const cart = makeCart(sidebar, openButton)

    await handleDocumentClick(makeMouseEvent(openButton), cart)

    expect(mockedToggleCart).not.toHaveBeenCalled()
  })

  it('should do nothing when sidebar is null in elements', async () => {
    mockedHandleBuy.mockReturnValueOnce(false)
    mockedHandleRemove.mockResolvedValueOnce(false)

    const cart = makeCart(null, document.createElement('button'))

    await handleDocumentClick(makeMouseEvent(document.createElement('div')), cart)

    expect(mockedToggleCart).not.toHaveBeenCalled()
  })

  it('should close cart when event target is not a Node', async () => {
    mockedHandleBuy.mockReturnValueOnce(false)
    mockedHandleRemove.mockResolvedValueOnce(false)

    const sidebar = document.createElement('div')
    const openButton = document.createElement('button')

    document.body.append(sidebar, openButton)

    const cart = makeCart(sidebar, openButton)
    const event = new MouseEvent('click')

    Object.defineProperty(event, 'target', { value: null, writable: false })

    await handleDocumentClick(event, cart)

    expect(mockedToggleCart).toHaveBeenCalledWith(cart, false)
  })

  it('should not close cart when cart has no elements', async () => {
    mockedHandleBuy.mockReturnValueOnce(false)
    mockedHandleRemove.mockResolvedValueOnce(false)

    const cart: CartInstance = { isOpen: true, elements: null }

    await handleDocumentClick(makeMouseEvent(document.createElement('div')), cart)

    expect(mockedToggleCart).not.toHaveBeenCalled()
  })

  it('should not close cart when click happened within 200ms of loading', async () => {
    mockedHandleBuy.mockReturnValueOnce(false)
    mockedHandleRemove.mockResolvedValueOnce(false)

    const sidebar = document.createElement('div')
    const openButton = document.createElement('button')
    const outsideTarget = document.createElement('span')
    const loadingEl = document.createElement('div')
    loadingEl.className = 'loading'
    loadingEl.dataset.lastShown = String(Date.now() - 50)

    document.body.appendChild(sidebar)
    document.body.appendChild(openButton)
    document.body.appendChild(outsideTarget)
    document.body.appendChild(loadingEl)

    const cart = makeCart(sidebar, openButton)

    await handleDocumentClick(makeMouseEvent(outsideTarget), cart)

    expect(mockedToggleCart).not.toHaveBeenCalled()
  })
})
