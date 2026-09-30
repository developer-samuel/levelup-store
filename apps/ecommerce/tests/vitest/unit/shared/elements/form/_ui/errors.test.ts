import { showErrors, clearErrors } from '@/ts/shared/elements/form/_ui/errors'

function createForm(html: string): HTMLFormElement {
  const form = document.createElement('form')
  form.innerHTML = html

  document.body.appendChild(form)

  return form
}

afterEach(() => {
  document.body.innerHTML = ''
})

describe('showErrors()', () => {
  it('should do nothing when form is null', () => {
    expect(() => showErrors(null, '.field', { email: ['Required.'] })).not.toThrow()
  })

  it('should do nothing when errorGroupClass is null', () => {
    const form = createForm('<input name="email" />')
    expect(() => showErrors(form, null, { email: ['Required.'] })).not.toThrow()
  })

  it('should display an error message in the correct error div', () => {
    const form = createForm(`
      <div class="field">
        <input name="email" />
        <span class="error"></span>
      </div>
    `)

    showErrors(form, '.field', { email: ['Invalid email.'] })

    const errorDiv = form.querySelector('.error')
    expect(errorDiv?.textContent).toContain('Invalid email.')
  })

  it('should separate multiple messages with br elements', () => {
    const form = createForm(`
      <div class="field">
        <input name="email" />
        <span class="error"></span>
      </div>
    `)

    showErrors(form, '.field', { email: ['Required.', 'Must be valid.'] })

    const errorDiv = form.querySelector('.error')
    expect(errorDiv?.querySelector('br')).not.toBeNull()
    expect(errorDiv?.textContent).toContain('Required.')
    expect(errorDiv?.textContent).toContain('Must be valid.')
  })

  it('should display error message when messages is a string instead of array', () => {
    const form = createForm(`
      <div class="field">
        <input name="email" />
        <span class="error"></span>
      </div>
    `)

    showErrors(form, '.field', { email: 'Required.' as unknown as string[] })

    const errorDiv = form.querySelector('.error')

    expect(errorDiv?.textContent).toContain('Required.')
  })

  it('should skip fields with no matching input', () => {
    const form = createForm('<div class="field"><span class="error"></span></div>')

    expect(() => showErrors(form, '.field', { missing: ['Error.'] })).not.toThrow()
  })

  it('should skip inputs with no matching error div', () => {
    const form = createForm('<div class="field"><input name="email" /></div>')

    expect(() => showErrors(form, '.field', { email: ['Error.'] })).not.toThrow()
  })
})

describe('clearErrors()', () => {
  it('should do nothing when form is null', () => {
    expect(() => clearErrors(null)).not.toThrow()
  })

  it('should clear all error div text content', () => {
    const form = createForm(`
      <span class="error">Some error</span>
      <span class="error">Another error</span>
    `)

    clearErrors(form)

    form.querySelectorAll('.error').forEach((el) => {
      expect(el.textContent).toBe('')
    })
  })
})
