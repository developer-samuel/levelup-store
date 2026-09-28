import { mockNotyfAlert } from '@/tests/_support/mocks/plugins/notyf.mocks'

mockNotyfAlert()

vi.mock('@/ts/features/cart/_utils/cartResponse', () => ({
  getCartErrorMessage: vi.fn((e: unknown) => (e instanceof Error ? e.message : 'Cart error')),
}))

vi.mock('@/ts/features/cart/_ui/render', () => ({
  renderCart: vi.fn(),
}))

vi.mock('@/ts/features/cart/_services/cartService', () => ({
  cartAdd: vi.fn(),
  cartRemove: vi.fn(),
}))

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import type { CartAction } from '@/ts/features/cart/types'
import { renderCart } from '@/ts/features/cart/_ui/render'
import { cartAdd, cartRemove } from '@/ts/features/cart/_services/cartService'
import { handleCartAction } from '@/ts/features/cart/_handlers/cartActionHandler'

const mockedCartAdd = vi.mocked(cartAdd)
const mockedCartRemove = vi.mocked(cartRemove)
const mockedRenderCart = vi.mocked(renderCart)
const mockedNotyfSuccess = vi.mocked(NotyfAlert.success)
const mockedNotyfError = vi.mocked(NotyfAlert.error)

function makeEl(datasetKey: string, value: string): HTMLElement {
  const el = document.createElement('div')
  el.dataset[datasetKey] = value
  return el
}

describe('handleCartAction()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should do nothing when element has no id', async () => {
    const el = document.createElement('div')
    await handleCartAction(el, 'add')
    expect(mockedCartAdd).not.toHaveBeenCalled()
  })

  it('should call cartAdd for add action', async () => {
    mockedCartAdd.mockResolvedValueOnce({ success: true })
    const el = makeEl('variantId', '5')

    await handleCartAction(el, 'add')

    expect(mockedCartAdd).toHaveBeenCalledWith('5')
  })

  it('should call cartRemove for remove action', async () => {
    mockedCartRemove.mockResolvedValueOnce({ success: true })
    const el = makeEl('variantId', '7')

    await handleCartAction(el, 'remove')

    expect(mockedCartRemove).toHaveBeenCalledWith('7')
  })

  it('should do nothing when service returns null', async () => {
    mockedCartAdd.mockResolvedValueOnce(null)
    const el = makeEl('variantId', '1')

    await handleCartAction(el, 'add')

    expect(mockedRenderCart).not.toHaveBeenCalled()
  })

  it('should call renderCart with response data', async () => {
    const data = { success: true, totalItems: 3 }
    mockedCartAdd.mockResolvedValueOnce(data)
    const el = makeEl('variantId', '1')

    await handleCartAction(el, 'add')

    expect(mockedRenderCart).toHaveBeenCalledWith(data)
  })

  it('should show success alert when success=true and message present', async () => {
    mockedCartAdd.mockResolvedValueOnce({ success: true, message: 'Added to cart.' })
    const el = makeEl('variantId', '1')

    await handleCartAction(el, 'add')

    expect(mockedNotyfSuccess).toHaveBeenCalledWith('Added to cart.')
  })

  it('should show error alert when success=false and message present', async () => {
    mockedCartAdd.mockResolvedValueOnce({ success: false, message: 'Out of stock.' })
    const el = makeEl('variantId', '1')

    await handleCartAction(el, 'add')

    expect(mockedNotyfError).toHaveBeenCalledWith('Out of stock.')
  })

  it('should not show alert when message is empty', async () => {
    mockedCartAdd.mockResolvedValueOnce({ success: true, message: '' })
    const el = makeEl('variantId', '1')

    await handleCartAction(el, 'add')

    expect(mockedNotyfSuccess).not.toHaveBeenCalled()
    expect(mockedNotyfError).not.toHaveBeenCalled()
  })

  it('should show error alert on thrown exception', async () => {
    mockedCartAdd.mockRejectedValueOnce(new Error('Network error'))
    const el = makeEl('variantId', '1')

    await handleCartAction(el, 'add')

    expect(mockedNotyfError).toHaveBeenCalledWith('Network error')
  })

  it('should do nothing when action is unknown', async () => {
    const el = makeEl('variantId', '1')

    await handleCartAction(el, 'unknown' as unknown as CartAction)

    expect(mockedRenderCart).not.toHaveBeenCalled()
  })

  it('should do nothing when closest parent has no itemId', async () => {
    const wrapper = document.createElement('div')
    wrapper.className = 'cart__content-item'
    const btn = document.createElement('button')
    wrapper.appendChild(btn)
    document.body.appendChild(wrapper)

    await handleCartAction(btn, 'add')

    expect(mockedCartAdd).not.toHaveBeenCalled()

    document.body.innerHTML = ''
  })

  it('should read itemId from closest .cart__content-item when variantId missing', async () => {
    mockedCartRemove.mockResolvedValueOnce({ success: true })

    const wrapper = document.createElement('div')
    wrapper.className = 'cart__content-item'
    wrapper.dataset.itemId = '99'
    const btn = document.createElement('button')
    wrapper.appendChild(btn)
    document.body.appendChild(wrapper)

    await handleCartAction(btn, 'remove')

    expect(mockedCartRemove).toHaveBeenCalledWith('99')

    document.body.innerHTML = ''
  })
})
