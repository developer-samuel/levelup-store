import { show, hide, toggle, isVisible } from '@/ts/features/products/list/_ui/visibility'

describe('show()', () => {
  it('should set display to block', () => {
    const el = document.createElement('div')
    show(el)
    expect(el.style.display).toBe('block')
  })

  it('should do nothing when element is null', () => {
    expect(() => show(null)).not.toThrow()
  })
})

describe('hide()', () => {
  it('should set display to none', () => {
    const el = document.createElement('div')
    hide(el)
    expect(el.style.display).toBe('none')
  })

  it('should do nothing when element is null', () => {
    expect(() => hide(null)).not.toThrow()
  })
})

describe('toggle()', () => {
  it('should set display to block when condition is true', () => {
    const el = document.createElement('div')
    toggle(el, true)
    expect(el.style.display).toBe('block')
  })

  it('should set display to none when condition is false', () => {
    const el = document.createElement('div')
    toggle(el, false)
    expect(el.style.display).toBe('none')
  })

  it('should do nothing when element is null', () => {
    expect(() => toggle(null, true)).not.toThrow()
  })
})

describe('isVisible()', () => {
  it('should return false when element is null', () => {
    expect(isVisible(null)).toBe(false)
  })

  it('should return false when display is none', () => {
    const el = document.createElement('div')
    el.style.display = 'none'
    document.body.appendChild(el)
    expect(isVisible(el)).toBe(false)
    document.body.innerHTML = ''
  })

  it('should return true when display is not none', () => {
    const el = document.createElement('div')
    el.style.display = 'block'
    document.body.appendChild(el)
    expect(isVisible(el)).toBe(true)
    document.body.innerHTML = ''
  })
})
