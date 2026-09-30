import { makeInput } from '@/tests/_support/fakers/dom.fakers'

import { updatePriceOutputs } from '@/ts/features/products/list/_ui/price'

describe('updatePriceOutputs()', () => {
  it('should do nothing when minInput is null', () => {
    const maxInput = makeInput('100')
    const minOutput = document.createElement('span')
    const maxOutput = document.createElement('span')
    expect(() => updatePriceOutputs(null, maxInput, minOutput, maxOutput)).not.toThrow()
    expect(minOutput.textContent).toBe('')
  })

  it('should do nothing when maxInput is null', () => {
    const minInput = makeInput('10')
    const minOutput = document.createElement('span')
    const maxOutput = document.createElement('span')
    expect(() => updatePriceOutputs(minInput, null, minOutput, maxOutput)).not.toThrow()
    expect(maxOutput.textContent).toBe('')
  })

  it('should set minOutput textContent to minInput value with € suffix', () => {
    const minOutput = document.createElement('span')
    const maxOutput = document.createElement('span')
    updatePriceOutputs(makeInput('20'), makeInput('100'), minOutput, maxOutput)
    expect(minOutput.textContent).toBe('20 €')
  })

  it('should set maxOutput textContent to maxInput value with € suffix', () => {
    const minOutput = document.createElement('span')
    const maxOutput = document.createElement('span')
    updatePriceOutputs(makeInput('20'), makeInput('150'), minOutput, maxOutput)
    expect(maxOutput.textContent).toBe('150 €')
  })

  it('should set output to " €" when input value is empty', () => {
    const minOutput = document.createElement('span')
    const maxOutput = document.createElement('span')
    updatePriceOutputs(makeInput(''), makeInput(''), minOutput, maxOutput)
    expect(minOutput.textContent).toBe(' €')
    expect(maxOutput.textContent).toBe(' €')
  })

  it('should not throw when minOutput is null', () => {
    expect(() =>
      updatePriceOutputs(makeInput('10'), makeInput('100'), null, document.createElement('span')),
    ).not.toThrow()
  })

  it('should not throw when maxOutput is null', () => {
    expect(() =>
      updatePriceOutputs(makeInput('10'), makeInput('100'), document.createElement('span'), null),
    ).not.toThrow()
  })
})
