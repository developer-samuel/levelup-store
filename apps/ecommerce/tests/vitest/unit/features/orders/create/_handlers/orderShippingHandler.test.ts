vi.mock('@/ts/features/orders/create/_ui/shipping', () => ({
  updateShipping: vi.fn(),
  toggleShipping: vi.fn(),
}))

import { makeCheckbox } from '@/tests/_support/fakers/dom.fakers'

import type { HtmlInputList } from '@/ts/shared/types'

import { updateShipping, toggleShipping } from '@/ts/features/orders/create/_ui/shipping'
import { handleShippingToggle } from '@/ts/features/orders/create/_handlers/orderShippingHandler'

const mockedUpdateShipping = vi.mocked(updateShipping)
const mockedToggleShipping = vi.mocked(toggleShipping)

describe('handleShippingToggle()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should call updateShipping with true when checkbox is checked', () => {
    const shippingData = document.createElement('div')
    const inputs = [] as unknown as HtmlInputList

    handleShippingToggle(makeCheckbox('', true), shippingData, inputs)

    expect(mockedUpdateShipping).toHaveBeenCalledWith(shippingData, true)
  })

  it('should call updateShipping with false when checkbox is unchecked', () => {
    const shippingData = document.createElement('div')
    const inputs = [] as unknown as HtmlInputList

    handleShippingToggle(makeCheckbox('', false), shippingData, inputs)

    expect(mockedUpdateShipping).toHaveBeenCalledWith(shippingData, false)
  })

  it('should call toggleShipping with true when checkbox is checked', () => {
    const inputs = [] as unknown as HtmlInputList

    handleShippingToggle(makeCheckbox('', true), document.createElement('div'), inputs)

    expect(mockedToggleShipping).toHaveBeenCalledWith(inputs, true)
  })

  it('should call toggleShipping with false when checkbox is unchecked', () => {
    const inputs = [] as unknown as HtmlInputList

    handleShippingToggle(makeCheckbox('', false), document.createElement('div'), inputs)

    expect(mockedToggleShipping).toHaveBeenCalledWith(inputs, false)
  })
})
