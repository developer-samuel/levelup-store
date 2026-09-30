import { sleep } from '@/ts/shared/utils/sleep'

describe('sleep()', () => {
  beforeEach(() => {
    vi.useFakeTimers()
  })

  afterEach(() => {
    vi.useRealTimers()
  })

  it('should not resolve before the given delay', async () => {
    let resolved = false

    void sleep(1000).then(() => {
      resolved = true
    })

    vi.advanceTimersByTime(999)
    await Promise.resolve()

    expect(resolved).toBe(false)
  })

  it('should resolve after the given delay', async () => {
    let resolved = false

    void sleep(1000).then(() => {
      resolved = true
    })

    vi.advanceTimersByTime(1000)
    await Promise.resolve()

    expect(resolved).toBe(true)
  })

  it('should resolve immediately when delay is 0', async () => {
    let resolved = false

    void sleep(0).then(() => {
      resolved = true
    })

    vi.advanceTimersByTime(0)
    await Promise.resolve()

    expect(resolved).toBe(true)
  })
})
