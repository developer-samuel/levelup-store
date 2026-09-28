import { makeInput } from '@/tests/_support/fakers/dom.fakers'

import { clampMinPrice, clampMaxPrice } from '@/ts/features/products/list/_utils/price'

describe('clampMinPrice()', () => {
  it('should not change maxInput when minInput <= maxInput', () => {
    const min = makeInput('10', 'number')
    const max = makeInput('100', 'number')
    clampMinPrice(min, max)
    expect(max.value).toBe('100')
  })

  it('should set maxInput value to minInput value when minInput > maxInput', () => {
    const min = makeInput('150', 'number')
    const max = makeInput('100', 'number')
    clampMinPrice(min, max)
    expect(max.value).toBe('150')
  })

  it('should not change maxInput when minInput equals maxInput', () => {
    const min = makeInput('50', 'number')
    const max = makeInput('50', 'number')
    clampMinPrice(min, max)
    expect(max.value).toBe('50')
  })
})

describe('clampMaxPrice()', () => {
  it('should not change minInput when maxInput >= minInput', () => {
    const min = makeInput('10', 'number')
    const max = makeInput('100', 'number')
    clampMaxPrice(min, max)
    expect(min.value).toBe('10')
  })

  it('should set minInput value to maxInput value when maxInput < minInput', () => {
    const min = makeInput('100', 'number')
    const max = makeInput('50', 'number')
    clampMaxPrice(min, max)
    expect(min.value).toBe('50')
  })

  it('should not change minInput when maxInput equals minInput', () => {
    const min = makeInput('50', 'number')
    const max = makeInput('50', 'number')
    clampMaxPrice(min, max)
    expect(min.value).toBe('50')
  })
})
