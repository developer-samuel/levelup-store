import { makeFormAlert, makeFormErrorsHandler } from '@/tests/_support/fakers/form.fakers'

import type { FormErrorsHandler, FormSubmitHandler } from '@/ts/shared/elements/form/types'
import { attachSubmitListener } from '@/ts/shared/elements/form/_listeners/submitListener'

function submitForm(form: HTMLFormElement): void {
  form.dispatchEvent(new SubmitEvent('submit', { bubbles: true, cancelable: true }))
}

describe('attachSubmitListener()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should do nothing when form is null', () => {
    const handler: FormSubmitHandler = vi.fn().mockResolvedValue(undefined)

    expect(() => attachSubmitListener(null, handler, makeFormErrorsHandler(), makeFormAlert())).not.toThrow()
    expect(handler).not.toHaveBeenCalled()
  })

  it('should do nothing when submitHandler is null', () => {
    const form = document.createElement('form')

    expect(() =>
      attachSubmitListener(form, null as unknown as FormSubmitHandler, makeFormErrorsHandler(), makeFormAlert()),
    ).not.toThrow()
  })

  it('should prevent default form submission', () => {
    const form = document.createElement('form')
    const handler: FormSubmitHandler = vi.fn().mockResolvedValue(undefined)
    const alert = makeFormAlert()
    const errors = makeFormErrorsHandler()

    attachSubmitListener(form, handler, errors, alert)

    const event = new SubmitEvent('submit', { bubbles: true, cancelable: true })
    const preventDefaultSpy = vi.spyOn(event, 'preventDefault')
    form.dispatchEvent(event)

    expect(preventDefaultSpy).toHaveBeenCalledTimes(1)
  })

  it('should call submitHandler on form submit', async () => {
    const form = document.createElement('form')
    const handler: FormSubmitHandler = vi.fn().mockResolvedValue(undefined)
    const alert = makeFormAlert()
    const errors = makeFormErrorsHandler()

    attachSubmitListener(form, handler, errors, alert)
    submitForm(form)

    await vi.waitFor(() => {
      expect(handler).toHaveBeenCalledTimes(1)
    })
  })

  it('should pass form, alert, and errors to submitHandler', async () => {
    const form = document.createElement('form')
    const handler: FormSubmitHandler = vi.fn().mockResolvedValue(undefined)
    const alert = makeFormAlert()
    const errors = makeFormErrorsHandler()

    attachSubmitListener(form, handler, errors, alert)
    submitForm(form)

    await vi.waitFor(() => {
      expect(handler).toHaveBeenCalledWith(form, alert, errors)
    })
  })

  it('should call errors.clear before calling submitHandler', async () => {
    const form = document.createElement('form')
    const callOrder: string[] = []
    const errors = {
      clear: vi.fn(() => callOrder.push('clear')),
      show: vi.fn(),
    } satisfies FormErrorsHandler
    const handler: FormSubmitHandler = vi.fn(() => {
      callOrder.push('handler')
      return Promise.resolve()
    })

    attachSubmitListener(form, handler, errors, makeFormAlert())
    submitForm(form)

    await vi.waitFor(() => {
      expect(callOrder).toEqual(['clear', 'handler'])
    })
  })

  it('should prevent double submit while handler is in flight', async () => {
    const form = document.createElement('form')
    let resolveHandler!: () => void
    const handler: FormSubmitHandler = vi.fn(
      () =>
        new Promise<void>((resolve) => {
          resolveHandler = resolve
        }),
    )

    attachSubmitListener(form, handler, makeFormErrorsHandler(), makeFormAlert())
    submitForm(form)
    submitForm(form)

    expect(handler).toHaveBeenCalledTimes(1)

    resolveHandler()
    await vi.waitFor(() => {})
  })

  it('should allow resubmit after handler resolves', async () => {
    const form = document.createElement('form')
    const handler: FormSubmitHandler = vi.fn().mockResolvedValue(undefined)

    attachSubmitListener(form, handler, makeFormErrorsHandler(), makeFormAlert())
    submitForm(form)

    await vi.waitFor(() => expect(handler).toHaveBeenCalledTimes(1))

    submitForm(form)

    await vi.waitFor(() => expect(handler).toHaveBeenCalledTimes(2))
  })

  it('should allow resubmit after handler rejects', async () => {
    const form = document.createElement('form')
    const handler: FormSubmitHandler = vi.fn().mockRejectedValueOnce(new Error('fail')).mockResolvedValueOnce(undefined)

    attachSubmitListener(form, handler, makeFormErrorsHandler(), makeFormAlert())
    submitForm(form)

    await vi.waitFor(() => expect(handler).toHaveBeenCalledTimes(1))

    submitForm(form)

    await vi.waitFor(() => expect(handler).toHaveBeenCalledTimes(2))
  })
})
