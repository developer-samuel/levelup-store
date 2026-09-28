import { mockProductsListUiVisibility } from '@/tests/_support/mocks/features/products/list.mocks'

mockProductsListUiVisibility()

import { toggle } from '@/ts/features/products/list/_ui/visibility'
import { handleResize } from '@/ts/features/products/list/_handlers/resizeHandler'

const mockedToggle = vi.mocked(toggle)

describe('handleResize()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    vi.restoreAllMocks()
  })

  it('should not call toggle when width has not changed', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(1280)
    const filter = document.createElement('div')
    const lastWidth = { value: 1280 }

    handleResize(filter, lastWidth)

    expect(mockedToggle).not.toHaveBeenCalled()
  })

  it('should call toggle with true when width changes to >= 1280', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(1280)
    const filter = document.createElement('div')
    const lastWidth = { value: 768 }

    handleResize(filter, lastWidth)

    expect(mockedToggle).toHaveBeenCalledWith(filter, true)
  })

  it('should call toggle with false when width changes to < 1280', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(768)
    const filter = document.createElement('div')
    const lastWidth = { value: 1280 }

    handleResize(filter, lastWidth)

    expect(mockedToggle).toHaveBeenCalledWith(filter, false)
  })

  it('should call toggle with false when width changes to exactly 1279', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(1279)
    const filter = document.createElement('div')
    const lastWidth = { value: 1280 }

    handleResize(filter, lastWidth)

    expect(mockedToggle).toHaveBeenCalledWith(filter, false)
  })

  it('should update lastWidth after resize', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(1024)
    const filter = document.createElement('div')
    const lastWidth = { value: 768 }

    handleResize(filter, lastWidth)

    expect(lastWidth.value).toBe(1024)
  })
})
