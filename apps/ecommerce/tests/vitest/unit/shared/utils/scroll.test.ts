import { scrollToTop, scrollToContainer } from '@/ts/shared/utils/scroll'

describe('scrollToTop()', () => {
  it('should call window.scrollTo with top 0 and smooth behavior', () => {
    const spy = vi.spyOn(window, 'scrollTo').mockImplementation(() => {})
    scrollToTop()
    expect(spy).toHaveBeenCalledWith({ top: 0, behavior: 'smooth' })
    spy.mockRestore()
  })
})

describe('scrollToContainer()', () => {
  afterEach(() => {
    document.body.innerHTML = ''
  })

  it('should call scrollTo on documentElement when no element has scrollTop > 0', () => {
    document.documentElement.scrollTo = vi.fn()
    scrollToContainer()
    expect(document.documentElement.scrollTo).toHaveBeenCalledWith({ top: 0, behavior: 'smooth' })
  })

  it('should call scrollTo on first element with scrollTop > 0', () => {
    const el = document.createElement('div')
    document.body.appendChild(el)
    Object.defineProperty(el, 'scrollTop', { get: () => 100, configurable: true })
    el.scrollTo = vi.fn()
    scrollToContainer()
    expect(el.scrollTo).toHaveBeenCalledWith({ top: 0, behavior: 'smooth' })
  })
})
