import { makeInput } from '@/tests/_support/fakers/dom.fakers'

import { clampMinPrice, clampMaxPrice } from '@/ts/features/products/list/_utils/price'

describe('clampMinPrice()', () => {
  it.each([
    ['10', '100', '100'],
    ['150', '100', '150'],
    ['50', '50', '50'],
  ])('should result in maxInput=%s when min=%s max=%s', (minVal, maxVal, expectedMax) => {
    const min = makeInput(minVal, 'number')
    const max = makeInput(maxVal, 'number')
    clampMinPrice(min, max)
    expect(max.value).toBe(expectedMax)
  })
})

describe('clampMaxPrice()', () => {
  it.each([
    ['10', '100', '10'],
    ['100', '50', '50'],
    ['50', '50', '50'],
  ])('should result in minInput=%s when min=%s max=%s', (minVal, maxVal, expectedMin) => {
    const min = makeInput(minVal, 'number')
    const max = makeInput(maxVal, 'number')
    clampMaxPrice(min, max)
    expect(min.value).toBe(expectedMin)
  })
})
