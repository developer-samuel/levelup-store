import { mockProductsListUiVisibility } from '@/tests/_support/mocks/features/products/list.mocks'

mockProductsListUiVisibility()

import { hide } from '@/ts/features/products/list/_ui/visibility'
import { attachCloseButtonListener } from '@/ts/features/products/list/_listeners/closeButtonListener'

const mockedHide = vi.mocked(hide)

describe('attachCloseButtonListener()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    vi.restoreAllMocks()
  })

  it('should do nothing when closeBtn is null', () => {
    const filter = document.createElement('div')
    expect(() => attachCloseButtonListener(null, filter)).not.toThrow()
  })

  it('should do nothing when productFilter is null', () => {
    const btn = document.createElement('button')
    expect(() => attachCloseButtonListener(btn, null)).not.toThrow()
  })

  it('should call hide when clicked and innerWidth < 1280', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(768)
    const btn = document.createElement('button')
    const filter = document.createElement('div')

    attachCloseButtonListener(btn, filter)
    btn.click()

    expect(mockedHide).toHaveBeenCalledWith(filter)
  })

  it('should not call hide when clicked and innerWidth >= 1280', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(1280)
    const btn = document.createElement('button')
    const filter = document.createElement('div')

    attachCloseButtonListener(btn, filter)
    btn.click()

    expect(mockedHide).not.toHaveBeenCalled()
  })
})
