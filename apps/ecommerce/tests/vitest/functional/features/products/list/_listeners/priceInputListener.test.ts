import { attachPriceInputListeners } from '@/ts/features/products/list/_listeners/priceInputListener'

describe('attachPriceInputListeners()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should do nothing when minInput is null', () => {
    const max = document.createElement('input')
    const onMin = vi.fn()
    const onMax = vi.fn()

    expect(() => attachPriceInputListeners(null, max, onMin, onMax)).not.toThrow()
    expect(onMin).not.toHaveBeenCalled()
  })

  it('should do nothing when maxInput is null', () => {
    const min = document.createElement('input')
    const onMin = vi.fn()
    const onMax = vi.fn()

    expect(() => attachPriceInputListeners(min, null, onMin, onMax)).not.toThrow()
    expect(onMax).not.toHaveBeenCalled()
  })

  it('should call onMinChange when minInput fires input event', () => {
    const min = document.createElement('input')
    const max = document.createElement('input')
    const onMin = vi.fn()
    const onMax = vi.fn()

    attachPriceInputListeners(min, max, onMin, onMax)
    min.dispatchEvent(new Event('input'))

    expect(onMin).toHaveBeenCalledTimes(1)
  })

  it('should call onMaxChange when maxInput fires input event', () => {
    const min = document.createElement('input')
    const max = document.createElement('input')
    const onMin = vi.fn()
    const onMax = vi.fn()

    attachPriceInputListeners(min, max, onMin, onMax)
    max.dispatchEvent(new Event('input'))

    expect(onMax).toHaveBeenCalledTimes(1)
  })

  it('should not call onMaxChange when minInput fires', () => {
    const min = document.createElement('input')
    const max = document.createElement('input')
    const onMin = vi.fn()
    const onMax = vi.fn()

    attachPriceInputListeners(min, max, onMin, onMax)
    min.dispatchEvent(new Event('input'))

    expect(onMax).not.toHaveBeenCalled()
  })

  it('should not call onMinChange when maxInput fires', () => {
    const min = document.createElement('input')
    const max = document.createElement('input')
    const onMin = vi.fn()
    const onMax = vi.fn()

    attachPriceInputListeners(min, max, onMin, onMax)
    max.dispatchEvent(new Event('input'))

    expect(onMin).not.toHaveBeenCalled()
  })
})
