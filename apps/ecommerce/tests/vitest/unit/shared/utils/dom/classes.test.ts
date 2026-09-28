import { toggleClass } from '@/ts/shared/utils/dom/classes'

describe('toggleClass()', () => {
  let element: HTMLElement

  beforeEach(() => {
    element = document.createElement('div')
  })

  it('should add the class when condition is true', () => {
    toggleClass(element, 'active', true)
    expect(element.classList.contains('active')).toBe(true)
  })

  it('should remove the class when condition is false', () => {
    element.classList.add('active')
    toggleClass(element, 'active', false)
    expect(element.classList.contains('active')).toBe(false)
  })

  it('should not throw when element is null', () => {
    expect(() => toggleClass(null, 'active', true)).not.toThrow()
  })
})
