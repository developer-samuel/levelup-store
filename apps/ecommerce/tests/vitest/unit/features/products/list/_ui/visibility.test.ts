import { show, hide, toggle, isVisible } from '@/ts/features/products/list/_ui/visibility'

const ACTIVE_CLASS = 'products__filter--active'
const BACKDROP_ACTIVE_CLASS = 'products__filter-backdrop--active'

afterEach(() => {
  document.body.innerHTML = ''
})

describe('show()', () => {
  it('should add active class to filter element', () => {
    const el = document.createElement('div')
    show(el)
    expect(el.classList.contains(ACTIVE_CLASS)).toBe(true)
  })

  it('should set body overflow hidden when viewport is < 1280', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(768)
    const el = document.createElement('div')
    show(el)
    expect(document.body.style.overflow).toBe('hidden')
  })

  it('should not set body overflow when viewport is >= 1280', () => {
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(1280)
    document.body.style.overflow = ''
    const el = document.createElement('div')
    show(el)
    expect(document.body.style.overflow).toBe('')
  })

  it('should add active class to backdrop when present', () => {
    const backdrop = document.createElement('div')
    backdrop.id = 'filter-backdrop'
    document.body.appendChild(backdrop)
    const el = document.createElement('div')
    show(el)
    expect(backdrop.classList.contains(BACKDROP_ACTIVE_CLASS)).toBe(true)
  })

  it('should not throw when backdrop is absent', () => {
    const el = document.createElement('div')
    expect(() => show(el)).not.toThrow()
  })

  it('should do nothing when element is null', () => {
    expect(() => show(null)).not.toThrow()
  })
})

describe('hide()', () => {
  it('should remove active class from filter element', () => {
    const el = document.createElement('div')
    el.classList.add(ACTIVE_CLASS)
    hide(el)
    expect(el.classList.contains(ACTIVE_CLASS)).toBe(false)
  })

  it('should remove active class from backdrop when present', () => {
    const backdrop = document.createElement('div')
    backdrop.id = 'filter-backdrop'
    backdrop.classList.add(BACKDROP_ACTIVE_CLASS)
    document.body.appendChild(backdrop)
    const el = document.createElement('div')
    hide(el)
    expect(backdrop.classList.contains(BACKDROP_ACTIVE_CLASS)).toBe(false)
  })

  it('should do nothing when element is null', () => {
    expect(() => hide(null)).not.toThrow()
  })
})

describe('toggle()', () => {
  it('should add active class when condition is true', () => {
    const el = document.createElement('div')
    toggle(el, true)
    expect(el.classList.contains(ACTIVE_CLASS)).toBe(true)
  })

  it('should remove active class when condition is false', () => {
    const el = document.createElement('div')
    el.classList.add(ACTIVE_CLASS)
    toggle(el, false)
    expect(el.classList.contains(ACTIVE_CLASS)).toBe(false)
  })

  it('should toggle backdrop when backdrop is present', () => {
    const backdrop = document.createElement('div')
    backdrop.id = 'filter-backdrop'
    document.body.appendChild(backdrop)
    vi.spyOn(window, 'innerWidth', 'get').mockReturnValue(768)
    const el = document.createElement('div')
    toggle(el, true)
    expect(backdrop.classList.contains(BACKDROP_ACTIVE_CLASS)).toBe(true)
  })

  it('should not throw when backdrop is absent', () => {
    const el = document.createElement('div')
    expect(() => toggle(el, true)).not.toThrow()
  })

  it('should do nothing when element is null', () => {
    expect(() => toggle(null, true)).not.toThrow()
  })
})

describe('isVisible()', () => {
  it('should return false when element is null', () => {
    expect(isVisible(null)).toBe(false)
  })

  it('should return false when active class is absent', () => {
    const el = document.createElement('div')
    expect(isVisible(el)).toBe(false)
  })

  it('should return true when active class is present', () => {
    const el = document.createElement('div')
    el.classList.add(ACTIVE_CLASS)
    expect(isVisible(el)).toBe(true)
  })
})
