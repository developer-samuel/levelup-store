import { updateShipping, toggleShipping } from '@/ts/features/orders/create/_ui/shipping'

describe('updateShipping()', () => {
  it('should set opacity to 1 when enabled', () => {
    const el = document.createElement('div')
    updateShipping(el, true)
    expect(el.style.opacity).toBe('1')
  })

  it('should set opacity to 0.5 when disabled', () => {
    const el = document.createElement('div')
    updateShipping(el, false)
    expect(el.style.opacity).toBe('0.5')
  })

  it('should set pointerEvents to auto when enabled', () => {
    const el = document.createElement('div')
    updateShipping(el, true)
    expect(el.style.pointerEvents).toBe('auto')
  })

  it('should set pointerEvents to none when disabled', () => {
    const el = document.createElement('div')
    updateShipping(el, false)
    expect(el.style.pointerEvents).toBe('none')
  })
})

describe('toggleShipping()', () => {
  it('should remove disabled attribute from all inputs when enabled', () => {
    const inputs = [document.createElement('input'), document.createElement('input')]
    inputs.forEach((i) => i.setAttribute('disabled', 'true'))

    toggleShipping(inputs, true)

    inputs.forEach((i) => expect(i.hasAttribute('disabled')).toBe(false))
  })

  it('should add disabled attribute to all inputs when disabled', () => {
    const inputs = [document.createElement('input'), document.createElement('input')]

    toggleShipping(inputs, false)

    inputs.forEach((i) => expect(i.getAttribute('disabled')).toBe('true'))
  })

  it('should do nothing when inputs list is empty', () => {
    expect(() => toggleShipping([], true)).not.toThrow()
  })
})
