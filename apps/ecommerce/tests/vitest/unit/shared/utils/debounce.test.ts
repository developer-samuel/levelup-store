import debounce from '@/ts/shared/utils/debounce'

describe('debounce()', () => {
  beforeEach(() => {
    vi.useFakeTimers()
  })

  afterEach(() => {
    vi.useRealTimers()
  })

  it('should not call the callback before the delay expires', () => {
    const callback = vi.fn<() => void>()
    const debounced = debounce(callback, 300)

    debounced()

    vi.advanceTimersByTime(299)

    expect(callback).not.toHaveBeenCalled()
  })

  it('should call the callback after the delay expires', () => {
    const callback = vi.fn<() => void>()
    const debounced = debounce(callback, 300)

    debounced()

    vi.advanceTimersByTime(300)

    expect(callback).toHaveBeenCalledTimes(1)
  })

  it('should reset the timer on each call and invoke callback only once', () => {
    const callback = vi.fn<() => void>()
    const debounced = debounce(callback, 300)

    debounced()
    vi.advanceTimersByTime(200)
    debounced()
    vi.advanceTimersByTime(200)
    debounced()
    vi.advanceTimersByTime(300)

    expect(callback).toHaveBeenCalledTimes(1)
  })

  it('should call the callback with the correct arguments', () => {
    const callback = vi.fn<(a: string, b: number) => void>()
    const debounced = debounce(callback, 100)

    debounced('hello', 42)

    vi.advanceTimersByTime(100)

    expect(callback).toHaveBeenCalledWith('hello', 42)
  })
})
