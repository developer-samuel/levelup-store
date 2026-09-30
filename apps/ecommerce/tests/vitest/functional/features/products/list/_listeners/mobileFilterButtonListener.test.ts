import { mockProductsListUiVisibility } from '@/tests/_support/mocks/features/products/list.mocks'

mockProductsListUiVisibility()

import { show } from '@/ts/features/products/list/_ui/visibility'
import { attachMobileFilterButtonListener } from '@/ts/features/products/list/_listeners/mobileFilterButtonListener'

const mockedShow = vi.mocked(show)

describe('attachMobileFilterButtonListener()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    vi.restoreAllMocks()
  })

  it('should do nothing when mobileFilterBtn is null', () => {
    const filter = document.createElement('div')
    expect(() => attachMobileFilterButtonListener(null, filter)).not.toThrow()
  })

  it('should do nothing when productFilter is null', () => {
    const btn = document.createElement('button')
    expect(() => attachMobileFilterButtonListener(btn, null)).not.toThrow()
  })

  it('should call show when clicked and innerWidth < 1280', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(768)
    const btn = document.createElement('button')
    const filter = document.createElement('div')

    attachMobileFilterButtonListener(btn, filter)
    btn.click()

    expect(mockedShow).toHaveBeenCalledWith(filter)
  })

  it('should not call show when clicked and innerWidth >= 1280', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(1280)
    const btn = document.createElement('button')
    const filter = document.createElement('div')

    attachMobileFilterButtonListener(btn, filter)
    btn.click()

    expect(mockedShow).not.toHaveBeenCalled()
  })
})
