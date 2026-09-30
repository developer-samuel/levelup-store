import { HEADER_TOGGLE } from '@/ts/presentation/layout/header/_events/toggle'
import { attachScrollListener } from '@/ts/features/products/list/_listeners/scrollListener'

function dispatchToggle(hidden: boolean): void {
  document.dispatchEvent(new CustomEvent(HEADER_TOGGLE, { bubbles: true, detail: { hidden } }))
}

describe('attachScrollListener()', () => {
  beforeEach(() => {
    document.body.innerHTML = ''
  })

  it('should do nothing when both elements are absent', () => {
    expect(() => attachScrollListener()).not.toThrow()
  })

  it('should attach listener and toggle classes when filterMobile is present', () => {
    const filterMobile = document.createElement('div')
    filterMobile.className = 'products__filter-mobile'
    document.body.appendChild(filterMobile)

    attachScrollListener()
    dispatchToggle(true)

    expect(filterMobile.classList.contains('products__filter-mobile--header-hidden')).toBe(true)
  })

  it('should attach listener and toggle classes when cardOptions is present', () => {
    const cardOptions = document.createElement('div')
    cardOptions.className = 'products__card-options'
    document.body.appendChild(cardOptions)

    attachScrollListener()
    dispatchToggle(true)

    expect(cardOptions.classList.contains('products__card-options--header-hidden')).toBe(true)
  })

  it('should remove hidden classes on hidden false', () => {
    const filterMobile = document.createElement('div')
    filterMobile.className = 'products__filter-mobile'
    filterMobile.classList.add('products__filter-mobile--header-hidden')
    document.body.appendChild(filterMobile)

    attachScrollListener()
    dispatchToggle(false)

    expect(filterMobile.classList.contains('products__filter-mobile--header-hidden')).toBe(false)
  })
})
