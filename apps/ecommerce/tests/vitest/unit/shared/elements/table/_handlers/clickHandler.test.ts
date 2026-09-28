import { handleTableClick } from '@/ts/shared/elements/table/_handlers/clickHandler'

function makeEvent(target: HTMLElement): MouseEvent {
  const event = new MouseEvent('click', { bubbles: true, cancelable: true })
  Object.defineProperty(event, 'target', { value: target, writable: false })
  return event
}

describe('handleTableClick()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should do nothing when target is not an HTMLElement', () => {
    const event = { target: null, preventDefault: vi.fn() } as unknown as Event
    expect(() => handleTableClick(event, { onClick: vi.fn() })).not.toThrow()
    expect(event.preventDefault).not.toHaveBeenCalled()
  })

  it('should do nothing when no matching element found via selector', () => {
    const div = document.createElement('div')
    const event = makeEvent(div)
    const preventDefaultSpy = vi.spyOn(event, 'preventDefault')

    handleTableClick(event, { selector: '.btn', onClick: vi.fn() })

    expect(preventDefaultSpy).not.toHaveBeenCalled()
  })

  it('should not prevent default when neither onClick nor confirmMessage provided', () => {
    const btn = document.createElement('button')
    const event = makeEvent(btn)
    const preventDefaultSpy = vi.spyOn(event, 'preventDefault')

    handleTableClick(event, {})

    expect(preventDefaultSpy).not.toHaveBeenCalled()
  })

  it('should prevent default when onClick is provided', () => {
    const btn = document.createElement('button')
    const event = makeEvent(btn)
    const preventDefaultSpy = vi.spyOn(event, 'preventDefault')

    handleTableClick(event, { onClick: vi.fn() })

    expect(preventDefaultSpy).toHaveBeenCalledTimes(1)
  })

  it('should prevent default when confirmMessage is provided', () => {
    vi.spyOn(window, 'confirm').mockReturnValueOnce(false)
    const btn = document.createElement('button')
    const event = makeEvent(btn)
    const preventDefaultSpy = vi.spyOn(event, 'preventDefault')

    handleTableClick(event, { confirmMessage: 'Are you sure?' })

    expect(preventDefaultSpy).toHaveBeenCalledTimes(1)
  })

  it('should call onClick with the resolved element', () => {
    const btn = document.createElement('button')
    const onClick = vi.fn()

    handleTableClick(makeEvent(btn), { onClick })

    expect(onClick).toHaveBeenCalledWith(btn)
  })

  it('should not call onClick when confirm is cancelled', () => {
    vi.spyOn(window, 'confirm').mockReturnValueOnce(false)
    const btn = document.createElement('button')
    const onClick = vi.fn()

    handleTableClick(makeEvent(btn), { confirmMessage: 'Sure?', onClick })

    expect(onClick).not.toHaveBeenCalled()
  })

  it('should not throw when confirm is accepted but onClick is not provided', () => {
    vi.spyOn(window, 'confirm').mockReturnValueOnce(true)

    const btn = document.createElement('button')

    expect(() => handleTableClick(makeEvent(btn), { confirmMessage: 'Sure?' })).not.toThrow()
  })

  it('should call onClick when confirm is accepted', () => {
    vi.spyOn(window, 'confirm').mockReturnValueOnce(true)
    const btn = document.createElement('button')
    const onClick = vi.fn()

    handleTableClick(makeEvent(btn), { confirmMessage: 'Sure?', onClick })

    expect(onClick).toHaveBeenCalledWith(btn)
  })

  it('should allow default when anchor href contains hrefContains string', () => {
    const a = document.createElement('a')
    a.href = 'http://localhost/edit/1'
    const event = makeEvent(a)
    const preventDefaultSpy = vi.spyOn(event, 'preventDefault')

    handleTableClick(event, { hrefContains: ['edit'], onClick: vi.fn() })

    expect(preventDefaultSpy).not.toHaveBeenCalled()
  })

  it('should prevent default when anchor href does not contain hrefContains string', () => {
    const a = document.createElement('a')
    a.href = 'http://localhost/delete/1'
    const event = makeEvent(a)
    const preventDefaultSpy = vi.spyOn(event, 'preventDefault')

    handleTableClick(event, { hrefContains: ['edit'], onClick: vi.fn() })

    expect(preventDefaultSpy).toHaveBeenCalledTimes(1)
  })

  it('should use target itself when no selector and no a/button ancestor found', () => {
    const div = document.createElement('div')
    const onClick = vi.fn()

    handleTableClick(makeEvent(div), { onClick })

    expect(onClick).toHaveBeenCalledWith(div)
  })

  it('should not skip click when hrefContains is set but element is not an anchor', () => {
    const btn = document.createElement('button')
    const onClick = vi.fn()

    handleTableClick(makeEvent(btn), { hrefContains: ['edit'], onClick })

    expect(onClick).toHaveBeenCalledWith(btn)
  })

  it('should resolve element via custom selector', () => {
    const wrapper = document.createElement('div')
    wrapper.className = 'row-action'
    const inner = document.createElement('span')
    wrapper.appendChild(inner)
    document.body.appendChild(wrapper)

    const onClick = vi.fn()

    handleTableClick(makeEvent(inner), { selector: '.row-action', onClick })

    expect(onClick).toHaveBeenCalledWith(wrapper)

    document.body.innerHTML = ''
  })
})
