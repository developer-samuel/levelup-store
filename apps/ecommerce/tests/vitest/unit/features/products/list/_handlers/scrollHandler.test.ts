import { handleStickyOffsetToggle } from '@/ts/features/products/list/_handlers/scrollHandler'

function makeToggleEvent(hidden: boolean): CustomEvent<{ hidden: boolean }> {
  return new CustomEvent('header:toggle', { detail: { hidden } })
}

describe('handleStickyOffsetToggle()', () => {
  it('should add hidden class to filterMobile when hidden is true', () => {
    const filterMobile = document.createElement('div')
    const cardOptions = document.createElement('div')

    handleStickyOffsetToggle(makeToggleEvent(true), filterMobile, cardOptions)

    expect(filterMobile.classList.contains('products__filter-mobile--header-hidden')).toBe(true)
  })

  it('should add hidden class to cardOptions when hidden is true', () => {
    const filterMobile = document.createElement('div')
    const cardOptions = document.createElement('div')

    handleStickyOffsetToggle(makeToggleEvent(true), filterMobile, cardOptions)

    expect(cardOptions.classList.contains('products__card-options--header-hidden')).toBe(true)
  })

  it('should remove hidden class from filterMobile when hidden is false', () => {
    const filterMobile = document.createElement('div')
    filterMobile.classList.add('products__filter-mobile--header-hidden')
    const cardOptions = document.createElement('div')

    handleStickyOffsetToggle(makeToggleEvent(false), filterMobile, cardOptions)

    expect(filterMobile.classList.contains('products__filter-mobile--header-hidden')).toBe(false)
  })

  it('should remove hidden class from cardOptions when hidden is false', () => {
    const filterMobile = document.createElement('div')
    const cardOptions = document.createElement('div')
    cardOptions.classList.add('products__card-options--header-hidden')

    handleStickyOffsetToggle(makeToggleEvent(false), filterMobile, cardOptions)

    expect(cardOptions.classList.contains('products__card-options--header-hidden')).toBe(false)
  })

  it('should handle null filterMobile without throwing', () => {
    const cardOptions = document.createElement('div')

    expect(() => handleStickyOffsetToggle(makeToggleEvent(true), null, cardOptions)).not.toThrow()
  })

  it('should handle null cardOptions without throwing', () => {
    const filterMobile = document.createElement('div')

    expect(() => handleStickyOffsetToggle(makeToggleEvent(true), filterMobile, null)).not.toThrow()
  })
})
