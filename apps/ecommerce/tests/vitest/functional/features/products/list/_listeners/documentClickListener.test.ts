import { mockProductsListUiVisibility } from '@/tests/_support/mocks/features/products/list.mocks'

mockProductsListUiVisibility()

import { hide, isVisible } from '@/ts/features/products/list/_ui/visibility'
import { attachDocumentClickListener } from '@/ts/features/products/list/_listeners/documentClickListener'

const mockedHide = vi.mocked(hide)
const mockedIsVisible = vi.mocked(isVisible)

function hiddenWithFilter(filter: HTMLElement): boolean {
  return mockedHide.mock.calls.map((c) => c[0]).includes(filter)
}

describe('attachDocumentClickListener()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    vi.restoreAllMocks()
  })

  it('should do nothing when mobileFilterBtn is null', () => {
    const filter = document.createElement('div')
    expect(() => attachDocumentClickListener(null, filter)).not.toThrow()
  })

  it('should do nothing when productFilter is null', () => {
    const btn = document.createElement('button')
    expect(() => attachDocumentClickListener(btn, null)).not.toThrow()
  })

  it('should not call hide when innerWidth >= 1280', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(1280)
    mockedIsVisible.mockReturnValue(true)

    const btn = document.createElement('button')
    const filter = document.createElement('div')
    document.body.appendChild(btn)
    document.body.appendChild(filter)

    attachDocumentClickListener(btn, filter)
    document.dispatchEvent(new MouseEvent('click', { bubbles: true }))

    expect(hiddenWithFilter(filter)).toBe(false)

    document.body.innerHTML = ''
  })

  it('should call hide when clicking outside filter and btn on mobile', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(768)
    mockedIsVisible.mockReturnValue(true)

    const btn = document.createElement('button')
    const filter = document.createElement('div')
    const outside = document.createElement('span')
    document.body.append(btn, filter, outside)

    attachDocumentClickListener(btn, filter)
    outside.dispatchEvent(new MouseEvent('click', { bubbles: true }))

    expect(mockedHide).toHaveBeenCalledWith(filter)

    document.body.innerHTML = ''
  })

  it('should not call hide when clicking inside filter on mobile', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(768)
    mockedIsVisible.mockReturnValue(true)

    const btn = document.createElement('button')
    const filter = document.createElement('div')
    const inner = document.createElement('span')
    filter.appendChild(inner)
    document.body.append(btn, filter)

    attachDocumentClickListener(btn, filter)
    inner.dispatchEvent(new MouseEvent('click', { bubbles: true }))

    expect(hiddenWithFilter(filter)).toBe(false)

    document.body.innerHTML = ''
  })

  it('should not call hide when clicking the mobileFilterBtn', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(768)
    mockedIsVisible.mockReturnValue(true)

    const btn = document.createElement('button')
    const filter = document.createElement('div')
    document.body.append(btn, filter)

    attachDocumentClickListener(btn, filter)
    btn.dispatchEvent(new MouseEvent('click', { bubbles: true }))

    expect(hiddenWithFilter(filter)).toBe(false)

    document.body.innerHTML = ''
  })

  it('should call hide when event target is not a Node', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(768)
    mockedIsVisible.mockReturnValue(true)

    const btn = document.createElement('button')
    const filter = document.createElement('div')
    document.body.append(btn, filter)

    attachDocumentClickListener(btn, filter)

    const event = new MouseEvent('click', { bubbles: true })
    Object.defineProperty(event, 'target', { value: null, writable: false })
    document.dispatchEvent(event)

    expect(hiddenWithFilter(filter)).toBe(true)

    document.body.innerHTML = ''
  })

  it('should not call hide when filter is not visible', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(768)
    mockedIsVisible.mockReturnValue(false)

    const btn = document.createElement('button')
    const filter = document.createElement('div')
    const outside = document.createElement('span')
    document.body.append(btn, filter, outside)

    attachDocumentClickListener(btn, filter)
    outside.dispatchEvent(new MouseEvent('click', { bubbles: true }))

    expect(hiddenWithFilter(filter)).toBe(false)

    document.body.innerHTML = ''
  })
})
