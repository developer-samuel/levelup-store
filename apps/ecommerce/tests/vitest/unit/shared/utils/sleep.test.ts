import { sleep } from '@/ts/shared/utils/sleep'

describe('sleep()', () => {
  beforeEach(() => {
    vi.useFakeTimers()
  })

  afterEach(() => {
    vi.useRealTimers()
  })

  it.each([
    [1000, 999, false],
    [1000, 1000, true],
    [0, 0, true],
  ] as [number, number, boolean][])(
    'should resolve=%s after advancing %ims of %ims delay',
    async (delay, advance, expected) => {
      let resolved = false

      void sleep(delay).then(() => {
        resolved = true
      })

      vi.advanceTimersByTime(advance)
      await Promise.resolve()

      expect(resolved).toBe(expected)
    },
  )
})
