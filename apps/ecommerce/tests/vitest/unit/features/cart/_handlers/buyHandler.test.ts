import { mockUtilsDebouce } from '@/tests/_support/mocks/shared/utils.mocks'
import { mockSharedEventsLoading } from '@/tests/_support/mocks/shared/events.mocks'
import { mockNotyfAlert } from '@/tests/_support/mocks/plugins/notyf.mocks'
import { mockCartUiToggle, mockCartActionHandler } from '@/tests/_support/mocks/features/cart.mocks'

mockUtilsDebouce()
mockSharedEventsLoading()
mockNotyfAlert()
mockCartUiToggle()
mockCartActionHandler()

vi.mock('@/ts/core/jwt/isAuth', () => ({
  isAuth: vi.fn(),
}))

import { makeMouseEvent } from '@/tests/_support/fakers/events.fakers'

import { isAuth } from '@/ts/core/jwt/isAuth'

import { dispatchLoadingShow, dispatchLoadingHide } from '@/ts/shared/events/loading'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import type { CartInstance } from '@/ts/features/cart/types'
import { toggleCart } from '@/ts/features/cart/_ui/toggle'
import { handleBuy } from '@/ts/features/cart/_handlers/buyHandler'
import { handleCartAction } from '@/ts/features/cart/_handlers/cartActionHandler'

const mockedIsAuth = vi.mocked(isAuth)
const mockedHandleCartAction = vi.mocked(handleCartAction)
const mockedToggleCart = vi.mocked(toggleCart)
const mockedLoadingShow = vi.mocked(dispatchLoadingShow)
const mockedLoadingHide = vi.mocked(dispatchLoadingHide)
const mockedNotyfError = vi.mocked(NotyfAlert.error)

function makeCart(warningBox: HTMLElement | null = null): CartInstance {
  return {
    isOpen: false,
    elements: {
      openButton: null,
      sidebar: null,
      closeButton: null,
      itemCountHeader: null,
      warningBox,
      warningCloseButton: null,
      carts: [],
    },
  }
}

function makeBuyButton(): HTMLElement {
  const el = document.createElement('button')
  el.className = 'buy-btn'
  return el
}

describe('handleBuy()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should return false when target has no .buy-btn', () => {
    const el = document.createElement('div')
    const result = handleBuy(makeMouseEvent(el), makeCart())
    expect(result).toBe(false)
  })

  it('should return true when buy button is found', () => {
    mockedIsAuth.mockReturnValue(true)
    mockedHandleCartAction.mockResolvedValueOnce(undefined)
    const result = handleBuy(makeMouseEvent(makeBuyButton()), makeCart())
    expect(result).toBe(true)
  })

  it('should return false when event target is null', () => {
    const result = handleBuy(makeMouseEvent(null), makeCart())
    expect(result).toBe(false)
  })

  it('should call dispatchLoadingShow when buy button clicked', async () => {
    mockedIsAuth.mockReturnValue(true)
    mockedHandleCartAction.mockResolvedValueOnce(undefined)

    handleBuy(makeMouseEvent(makeBuyButton()), makeCart())

    await vi.waitFor(() => expect(mockedLoadingShow).toHaveBeenCalledTimes(1))
  })

  it('should call dispatchLoadingHide after action completes', async () => {
    mockedIsAuth.mockReturnValue(true)
    mockedHandleCartAction.mockResolvedValueOnce(undefined)

    handleBuy(makeMouseEvent(makeBuyButton()), makeCart())

    await vi.waitFor(() => expect(mockedLoadingHide).toHaveBeenCalledTimes(1))
  })

  it('should open cart and show warningBox when user is not authenticated', async () => {
    mockedIsAuth.mockReturnValue(false)
    const warningBox = document.createElement('div')
    warningBox.style.display = 'none'
    const cart = makeCart(warningBox)

    handleBuy(makeMouseEvent(makeBuyButton()), cart)

    await vi.waitFor(() => {
      expect(mockedToggleCart).toHaveBeenCalledWith(cart, true)
      expect(warningBox.style.display).toBe('block')
    })
  })

  it('should not call handleCartAction when user is not authenticated', async () => {
    mockedIsAuth.mockReturnValue(false)

    handleBuy(makeMouseEvent(makeBuyButton()), makeCart())

    await vi.waitFor(() => expect(mockedLoadingHide).toHaveBeenCalled())
    expect(mockedHandleCartAction).not.toHaveBeenCalled()
  })

  it('should call handleCartAction with buy button and add action when authenticated', async () => {
    mockedIsAuth.mockReturnValue(true)
    mockedHandleCartAction.mockResolvedValueOnce(undefined)
    const btn = makeBuyButton()

    handleBuy(makeMouseEvent(btn), makeCart())

    await vi.waitFor(() => expect(mockedHandleCartAction).toHaveBeenCalledWith(btn, 'add'))
  })

  it('should open cart after successful add', async () => {
    mockedIsAuth.mockReturnValue(true)
    mockedHandleCartAction.mockResolvedValueOnce(undefined)
    const cart = makeCart()

    handleBuy(makeMouseEvent(makeBuyButton()), cart)

    await vi.waitFor(() => expect(mockedToggleCart).toHaveBeenCalledWith(cart, true))
  })

  it('should show error alert on exception', async () => {
    mockedIsAuth.mockReturnValue(true)
    mockedHandleCartAction.mockRejectedValueOnce(new Error('fail'))

    handleBuy(makeMouseEvent(makeBuyButton()), makeCart())

    await vi.waitFor(() => expect(mockedNotyfError).toHaveBeenCalledWith('Something went wrong. Please try again.'))
  })

  it('should call dispatchLoadingHide even after exception', async () => {
    mockedIsAuth.mockReturnValue(true)
    mockedHandleCartAction.mockRejectedValueOnce(new Error('fail'))

    handleBuy(makeMouseEvent(makeBuyButton()), makeCart())

    await vi.waitFor(() => expect(mockedLoadingHide).toHaveBeenCalledTimes(1))
  })

  it('should open cart without showing warningBox when warningBox is null', async () => {
    mockedIsAuth.mockReturnValue(false)
    const cart = makeCart(null)

    handleBuy(makeMouseEvent(makeBuyButton()), cart)

    await vi.waitFor(() => expect(mockedToggleCart).toHaveBeenCalledWith(cart, true))
  })
})
