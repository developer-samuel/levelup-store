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

  it.each([
    [768, 1280, true],
    [1280, 768, false],
    [1280, 1279, false],
  ] as [number, number, boolean][])(
    'should call toggle with $2 when width changes from $0 to $1',
    (lastWidthVal, innerWidthVal, expected) => {
      vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(innerWidthVal)
      const filter = document.createElement('div')
      const lastWidth = { value: lastWidthVal }

      handleResize(filter, lastWidth)

      expect(mockedToggle).toHaveBeenCalledWith(filter, expected)
    },
  )

  it('should update lastWidth after resize', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(1024)
    const filter = document.createElement('div')
    const lastWidth = { value: 768 }

    handleResize(filter, lastWidth)

    expect(lastWidth.value).toBe(1024)
  })
})
