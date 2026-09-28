vi.mock('@/ts/features/orders/create/_handlers/orderShippingHandler', () => ({
  handleShippingToggle: vi.fn(),
}))

import { makeCheckbox } from '@/tests/_support/fakers/dom.fakers'

import type { HtmlInputList } from '@/ts/shared/types'

import { handleShippingToggle } from '@/ts/features/orders/create/_handlers/orderShippingHandler'
import { attachOrderShippingChangeListener } from '@/ts/features/orders/create/_listeners/orderShippingListener'

const mockedHandleShippingToggle = vi.mocked(handleShippingToggle)

describe('attachOrderShippingChangeListener()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should do nothing when checkbox is null', () => {
    const shippingData = document.createElement('div')
    const inputs = [] as unknown as HtmlInputList

    expect(() => attachOrderShippingChangeListener(null, shippingData, inputs)).not.toThrow()
    expect(mockedHandleShippingToggle).not.toHaveBeenCalled()
  })

  it('should call handleShippingToggle when checkbox changes', () => {
    const checkbox = makeCheckbox()
    const shippingData = document.createElement('div')
    const inputs = [] as unknown as HtmlInputList

    attachOrderShippingChangeListener(checkbox, shippingData, inputs)
    checkbox.dispatchEvent(new Event('change'))

    expect(mockedHandleShippingToggle).toHaveBeenCalledTimes(1)
  })

  it('should pass checkbox, shippingData and inputs to handleShippingToggle', () => {
    const checkbox = makeCheckbox('', true)
    const shippingData = document.createElement('div')
    const inputs = [] as unknown as HtmlInputList

    attachOrderShippingChangeListener(checkbox, shippingData, inputs)
    checkbox.dispatchEvent(new Event('change'))

    expect(mockedHandleShippingToggle).toHaveBeenCalledWith(checkbox, shippingData, inputs)
  })
})
