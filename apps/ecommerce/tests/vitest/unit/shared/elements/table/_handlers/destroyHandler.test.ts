import { mockNotyfAlert } from '@/tests/_support/mocks/plugins/notyf.mocks'

mockNotyfAlert()

vi.mock('@/ts/core/http/_services/destroyData', () => ({
  destroyData: vi.fn(),
}))

vi.mock('@/ts/shared/elements/table/_ui/elements', () => ({
  removeRow: vi.fn(),
}))

import { destroyData } from '@/ts/core/http/_services/destroyData'

import { removeRow } from '@/ts/shared/elements/table/_ui/elements'
import { handleTableDestroy } from '@/ts/shared/elements/table/_handlers/destroyHandler'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

const mockedDestroyData = vi.mocked(destroyData)
const mockedNotyfSuccess = vi.mocked(NotyfAlert.success)
const mockedNotyfError = vi.mocked(NotyfAlert.error)
const mockedRemoveRow = vi.mocked(removeRow)

function makeEl(dataId?: string): HTMLElement {
  const el = document.createElement('a')

  if (dataId !== undefined) el.dataset.id = dataId

  return el
}

describe('handleTableDestroy()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should do nothing when element has no id', async () => {
    const el = document.createElement('a')

    await handleTableDestroy(el, { url: '/admin/products' })

    expect(mockedDestroyData).not.toHaveBeenCalled()
  })

  it('should call destroyData with url and id', async () => {
    mockedDestroyData.mockResolvedValueOnce({ success: true })
    const el = makeEl('5')

    await handleTableDestroy(el, { url: '/admin/products' })

    expect(mockedDestroyData).toHaveBeenCalledWith('/admin/products', '5')
  })

  it('should call removeRow and success alert on success', async () => {
    mockedDestroyData.mockResolvedValueOnce({ success: true, message: 'Deleted.' })
    const el = makeEl('5')

    await handleTableDestroy(el, { url: '/admin/products' })

    expect(mockedRemoveRow).toHaveBeenCalledWith(el)
    expect(mockedNotyfSuccess).toHaveBeenCalledWith('Deleted.')
  })

  it('should use default successMessage when response has no message', async () => {
    mockedDestroyData.mockResolvedValueOnce({ success: true })
    const el = makeEl('5')

    await handleTableDestroy(el, { url: '/admin/products', successMessage: 'Item removed.' })

    expect(mockedNotyfSuccess).toHaveBeenCalledWith('Item removed.')
  })

  it('should show error alert when success is false', async () => {
    mockedDestroyData.mockResolvedValueOnce({ success: false, message: 'Not found.' })
    const el = makeEl('5')

    await handleTableDestroy(el, { url: '/admin/products' })

    expect(mockedRemoveRow).not.toHaveBeenCalled()
    expect(mockedNotyfError).toHaveBeenCalledWith('Not found.')
  })

  it('should use default errorMessage when response has no message and success is false', async () => {
    mockedDestroyData.mockResolvedValueOnce({ success: false })
    const el = makeEl('5')

    await handleTableDestroy(el, { url: '/admin/products', errorMessage: 'Could not delete.' })

    expect(mockedNotyfError).toHaveBeenCalledWith('Could not delete.')
  })

  it('should show generic error alert on thrown exception', async () => {
    mockedDestroyData.mockRejectedValueOnce(new Error('Network error'))
    const el = makeEl('5')

    await handleTableDestroy(el, { url: '/admin/products' })

    expect(mockedNotyfError).toHaveBeenCalledWith('Something went wrong. Please try again.')
    expect(mockedRemoveRow).not.toHaveBeenCalled()
  })

  it('should read id from custom idAttr when dataset.id is missing', async () => {
    mockedDestroyData.mockResolvedValueOnce({ success: true })
    const el = document.createElement('a')
    el.setAttribute('data-product-id', '99')

    await handleTableDestroy(el, { url: '/admin/products', idAttr: 'data-product-id' })

    expect(mockedDestroyData).toHaveBeenCalledWith('/admin/products', '99')
  })
})
