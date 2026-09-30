import { mockUtilsDomQuery } from '@/tests/_support/mocks/shared/utils.mocks'

mockUtilsDomQuery()

import { queryAll } from '@/ts/shared/utils/dom/query'
import { resetFilterUI } from '@/ts/features/products/list/_ui/reset'

const mockedQueryAll = vi.mocked(queryAll)

describe('resetFilterUI()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    document.body.innerHTML = ''
    mockedQueryAll.mockReturnValue([] as unknown as NodeListOf<HTMLElement>)
  })

  it('should uncheck all brand checkboxes', () => {
    const cb1 = document.createElement('input') as HTMLInputElement
    const cb2 = document.createElement('input') as HTMLInputElement
    cb1.checked = true
    cb2.checked = true
    mockedQueryAll.mockImplementation((selector: string) => {
      if (selector === 'input[name="brand[]"]') return [cb1, cb2] as unknown as NodeListOf<HTMLElement>
      return [] as unknown as NodeListOf<HTMLElement>
    })

    resetFilterUI('100')

    expect(cb1.checked).toBe(false)
    expect(cb2.checked).toBe(false)
  })

  it('should remove active class from subtype elements', () => {
    const el = document.createElement('div')
    el.classList.add('products__filter-list-item--active')
    mockedQueryAll.mockImplementation((selector: string) => {
      if (selector === '[data-subtype]') return [el] as unknown as NodeListOf<HTMLElement>
      return [] as unknown as NodeListOf<HTMLElement>
    })

    resetFilterUI('100')

    expect(el.classList.contains('products__filter-list-item--active')).toBe(false)
  })

  it('should reset minPrice to 0 and dispatch input event', () => {
    const minPrice = document.createElement('input') as HTMLInputElement
    minPrice.id = 'minPrice'
    minPrice.value = '50'
    document.body.appendChild(minPrice)

    const spy = vi.fn()
    minPrice.addEventListener('input', spy)

    resetFilterUI('200')

    expect(minPrice.value).toBe('0')
    expect(spy).toHaveBeenCalled()
  })

  it('should reset maxPrice to initialMaxPrice and dispatch input event', () => {
    const maxPrice = document.createElement('input') as HTMLInputElement
    maxPrice.id = 'maxPrice'
    maxPrice.value = '50'
    document.body.appendChild(maxPrice)

    const spy = vi.fn()
    maxPrice.addEventListener('input', spy)

    resetFilterUI('200')

    expect(maxPrice.value).toBe('200')
    expect(spy).toHaveBeenCalled()
  })

  it('should not throw when minPrice and maxPrice elements are absent', () => {
    expect(() => resetFilterUI('100')).not.toThrow()
  })
})
