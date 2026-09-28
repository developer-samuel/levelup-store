import { mockSharedEventsLoading } from '@/tests/_support/mocks/shared/events.mocks'
import { mockNotyfAlert } from '@/tests/_support/mocks/plugins/notyf.mocks'
import { mockCartActionHandler } from '@/tests/_support/mocks/features/cart.mocks'

mockSharedEventsLoading()
mockCartActionHandler()
mockNotyfAlert()

import { makeMouseEvent } from '@/tests/_support/fakers/events.fakers'

import { dispatchLoadingShow, dispatchLoadingHide } from '@/ts/shared/events/loading'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import { handleRemove } from '@/ts/features/cart/_handlers/removeCartHandler'
import { handleCartAction } from '@/ts/features/cart/_handlers/cartActionHandler'

const mockedHandleCartAction = vi.mocked(handleCartAction)
const mockedLoadingShow = vi.mocked(dispatchLoadingShow)
const mockedLoadingHide = vi.mocked(dispatchLoadingHide)
const mockedNotyfError = vi.mocked(NotyfAlert.error)

function makeRemoveButton(): HTMLElement {
  const el = document.createElement('button')
  el.className = 'cart__content-destroy'
  return el
}

describe('handleRemove()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should return false when target has no .cart__content-destroy', async () => {
    const el = document.createElement('div')
    const result = await handleRemove(makeMouseEvent(el))
    expect(result).toBe(false)
  })

  it('should return true when remove button is found', async () => {
    mockedHandleCartAction.mockResolvedValueOnce(undefined)
    const result = await handleRemove(makeMouseEvent(makeRemoveButton()))
    expect(result).toBe(true)
  })

  it('should call dispatchLoadingShow on remove', async () => {
    mockedHandleCartAction.mockResolvedValueOnce(undefined)
    await handleRemove(makeMouseEvent(makeRemoveButton()))
    expect(mockedLoadingShow).toHaveBeenCalledTimes(1)
  })

  it('should call dispatchLoadingHide after remove', async () => {
    mockedHandleCartAction.mockResolvedValueOnce(undefined)
    await handleRemove(makeMouseEvent(makeRemoveButton()))
    expect(mockedLoadingHide).toHaveBeenCalledTimes(1)
  })

  it('should call handleCartAction with remove button and remove action', async () => {
    mockedHandleCartAction.mockResolvedValueOnce(undefined)
    const btn = makeRemoveButton()
    await handleRemove(makeMouseEvent(btn))
    expect(mockedHandleCartAction).toHaveBeenCalledWith(btn, 'remove')
  })

  it('should show error alert and still return true on exception', async () => {
    mockedHandleCartAction.mockRejectedValueOnce(new Error('fail'))
    const result = await handleRemove(makeMouseEvent(makeRemoveButton()))
    expect(mockedNotyfError).toHaveBeenCalledWith('Something went wrong. Please try again.')
    expect(result).toBe(true)
  })

  it('should call dispatchLoadingHide even after exception', async () => {
    mockedHandleCartAction.mockRejectedValueOnce(new Error('fail'))
    await handleRemove(makeMouseEvent(makeRemoveButton()))
    expect(mockedLoadingHide).toHaveBeenCalledTimes(1)
  })

  it('should return false when event target is null', async () => {
    const result = await handleRemove(makeMouseEvent(null))
    expect(result).toBe(false)
  })

  it('should return true immediately when already deleting', async () => {
    let resolveAction!: () => void
    mockedHandleCartAction.mockReturnValueOnce(
      new Promise<void>((resolve) => {
        resolveAction = resolve
      }),
    )

    const first = handleRemove(makeMouseEvent(makeRemoveButton()))
    const second = handleRemove(makeMouseEvent(makeRemoveButton()))

    resolveAction()
    const [, secondResult] = await Promise.all([first, second])

    expect(secondResult).toBe(true)
    expect(mockedHandleCartAction).toHaveBeenCalledTimes(1)
  })
})
