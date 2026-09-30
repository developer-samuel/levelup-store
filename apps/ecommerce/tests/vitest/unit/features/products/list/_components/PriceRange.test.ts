vi.mock('@/ts/features/products/list/_utils/price', () => ({
  clampMinPrice: vi.fn(),
  clampMaxPrice: vi.fn(),
}))

vi.mock('@/ts/features/products/list/_ui/price', () => ({
  updatePriceOutputs: vi.fn(),
}))

vi.mock('@/ts/features/products/list/_listeners/priceInputListener', () => ({
  attachPriceInputListeners: vi.fn(),
}))

import { updatePriceOutputs } from '@/ts/features/products/list/_ui/price'
import { clampMinPrice, clampMaxPrice } from '@/ts/features/products/list/_utils/price'
import { attachPriceInputListeners } from '@/ts/features/products/list/_listeners/priceInputListener'
import PriceRange from '@/ts/features/products/list/_components/PriceRange'

const mockedAttachPriceInputListeners = vi.mocked(attachPriceInputListeners)
const mockedClampMinPrice = vi.mocked(clampMinPrice)
const mockedClampMaxPrice = vi.mocked(clampMaxPrice)
const mockedUpdatePriceOutputs = vi.mocked(updatePriceOutputs)

function setupDOM(): {
  minInput: HTMLInputElement
  maxInput: HTMLInputElement
  minOutput: HTMLElement
  maxOutput: HTMLElement
} {
  const minInput = document.createElement('input')
  minInput.id = 'min-price'
  const maxInput = document.createElement('input')
  maxInput.id = 'max-price'
  const minOutput = document.createElement('span')
  minOutput.id = 'min-output'
  const maxOutput = document.createElement('span')
  maxOutput.id = 'max-output'

  document.body.append(minInput, maxInput, minOutput, maxOutput)

  return { minInput, maxInput, minOutput, maxOutput }
}

describe('PriceRange', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    document.body.innerHTML = ''
  })

  it('should call attachPriceInputListeners on construction', () => {
    setupDOM()
    new PriceRange('min-price', 'max-price', 'min-output', 'max-output')
    expect(mockedAttachPriceInputListeners).toHaveBeenCalledTimes(1)
  })

  it('should pass minPrice and maxPrice elements to attachPriceInputListeners', () => {
    const { minInput, maxInput } = setupDOM()
    new PriceRange('min-price', 'max-price', 'min-output', 'max-output')

    expect(mockedAttachPriceInputListeners).toHaveBeenCalledWith(
      minInput,
      maxInput,
      expect.any(Function),
      expect.any(Function),
    )
  })

  it('should pass null when minPrice element does not exist', () => {
    const maxInput = document.createElement('input')
    maxInput.id = 'max-price'
    document.body.appendChild(maxInput)

    new PriceRange('nonexistent', 'max-price', 'min-output', 'max-output')

    expect(mockedAttachPriceInputListeners).toHaveBeenCalledWith(
      null,
      maxInput,
      expect.any(Function),
      expect.any(Function),
    )
  })

  it('should call clampMinPrice and updatePriceOutputs when onMinChange callback is invoked', () => {
    const { minInput, maxInput, minOutput, maxOutput } = setupDOM()
    new PriceRange('min-price', 'max-price', 'min-output', 'max-output')

    const onMinChange = mockedAttachPriceInputListeners.mock.calls[0]?.[2] as () => void
    onMinChange()

    expect(mockedClampMinPrice).toHaveBeenCalledWith(minInput, maxInput)
    expect(mockedUpdatePriceOutputs).toHaveBeenCalledWith(minInput, maxInput, minOutput, maxOutput)
  })

  it('should call clampMaxPrice and updatePriceOutputs when onMaxChange callback is invoked', () => {
    const { minInput, maxInput, minOutput, maxOutput } = setupDOM()
    new PriceRange('min-price', 'max-price', 'min-output', 'max-output')

    const onMaxChange = mockedAttachPriceInputListeners.mock.calls[0]?.[3] as () => void
    onMaxChange()

    expect(mockedClampMaxPrice).toHaveBeenCalledWith(minInput, maxInput)
    expect(mockedUpdatePriceOutputs).toHaveBeenCalledWith(minInput, maxInput, minOutput, maxOutput)
  })

  it('should not call clampMinPrice when minPrice or maxPrice is null in onMinChange', () => {
    document.body.innerHTML = ''
    new PriceRange('nonexistent-min', 'nonexistent-max', 'min-output', 'max-output')

    const onMinChange = mockedAttachPriceInputListeners.mock.calls[0]?.[2] as () => void
    onMinChange()

    expect(mockedClampMinPrice).not.toHaveBeenCalled()
  })

  it('should not call clampMaxPrice when minPrice or maxPrice is null in onMaxChange', () => {
    document.body.innerHTML = ''
    new PriceRange('nonexistent-min', 'nonexistent-max', 'min-output', 'max-output')

    const onMaxChange = mockedAttachPriceInputListeners.mock.calls[0]?.[3] as () => void
    onMaxChange()

    expect(mockedClampMaxPrice).not.toHaveBeenCalled()
  })
})
