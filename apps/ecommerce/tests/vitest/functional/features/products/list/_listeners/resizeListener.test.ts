import { mockProductsListUiVisibility } from '@/tests/_support/mocks/features/products/list.mocks'

mockProductsListUiVisibility()

import { toggle } from '@/ts/features/products/list/_ui/visibility'
import { attachResizeListener } from '@/ts/features/products/list/_listeners/resizeListener'

const mockedToggle = vi.mocked(toggle)

describe('attachResizeListener()', () => {
  let cleanup: (() => void) | void

  beforeEach(() => {
    vi.clearAllMocks()
    vi.restoreAllMocks()
    cleanup = undefined
  })

  afterEach(() => {
    cleanup?.()
  })

  it('should do nothing when productFilter is null', () => {
    expect(() => attachResizeListener(null)).not.toThrow()
  })

  it('should call toggle with true when innerWidth changes to >= 1280', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValueOnce(768).mockReturnValue(1280)
    const filter = document.createElement('div')

    cleanup = attachResizeListener(filter)
    window.dispatchEvent(new Event('resize'))

    expect(mockedToggle).toHaveBeenCalledWith(filter, true)
  })

  it('should call toggle with false when innerWidth changes to < 1280', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValueOnce(1280).mockReturnValue(768)
    const filter = document.createElement('div')

    cleanup = attachResizeListener(filter)
    window.dispatchEvent(new Event('resize'))

    expect(mockedToggle).toHaveBeenCalledWith(filter, false)
  })

  it('should call toggle with false when innerWidth changes to exactly 1279', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValueOnce(1280).mockReturnValue(1279)
    const filter = document.createElement('div')

    cleanup = attachResizeListener(filter)
    window.dispatchEvent(new Event('resize'))

    expect(mockedToggle).toHaveBeenCalledWith(filter, false)
  })

  it('should not call toggle when innerWidth does not change', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(1280)
    const filter = document.createElement('div')

    cleanup = attachResizeListener(filter)
    window.dispatchEvent(new Event('resize'))

    expect(mockedToggle).not.toHaveBeenCalled()
  })

  it('should remove resize listener after cleanup is called', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValueOnce(768).mockReturnValue(1280)
    const filter = document.createElement('div')

    cleanup = attachResizeListener(filter)
    cleanup?.()
    cleanup = undefined

    window.dispatchEvent(new Event('resize'))

    expect(mockedToggle).not.toHaveBeenCalled()
  })
})
