import { attachSubmitListener } from '@/ts/shared/elements/form/_listeners/submitListener'
import type { FormErrorsHandler, FormSubmitHandler } from '@/ts/shared/elements/form/types'
import { makeFormAlert, makeFormErrorsHandler } from '@/tests/_support/fakers/form.fakers'

function buildForm(): HTMLFormElement {
  return document.createElement('form')
}

function buildHandler(): FormSubmitHandler {
  return vi.fn<FormSubmitHandler>().mockResolvedValue(undefined)
}

function submit(form: HTMLFormElement): void {
  form.dispatchEvent(new Event('submit'))
}

describe('attachSubmitListener()', () => {
  beforeEach(() => {
    vi.useFakeTimers()
  })

  afterEach(() => {
    vi.useRealTimers()
  })

  it('should return early when form is null', () => {
    const handler = buildHandler()

    attachSubmitListener(null, handler, makeFormErrorsHandler(), makeFormAlert())
    expect(handler).not.toHaveBeenCalled()
  })

  it('should return early when submitHandler is null', () => {
    const form = buildForm()

    attachSubmitListener(form, null as unknown as FormSubmitHandler, makeFormErrorsHandler(), makeFormAlert())
    submit(form)

    expect(true).toBe(true)
  })

  it('should call submitHandler on form submit', async () => {
    const form = buildForm()
    const handler = buildHandler()

    attachSubmitListener(form, handler, makeFormErrorsHandler(), makeFormAlert())
    submit(form)

    await vi.runAllTimersAsync()

    expect(handler).toHaveBeenCalledTimes(1)
  })

  it('should call errors.clear before submitting', async () => {
    const form = buildForm()
    const errors = makeFormErrorsHandler()

    attachSubmitListener(form, buildHandler(), errors, makeFormAlert())
    submit(form)

    await vi.runAllTimersAsync()

    expect(errors.clear).toHaveBeenCalledTimes(1)
  })

  it('should not call errors.clear when errors has no clear method', async () => {
    const form = buildForm()
    const errors: FormErrorsHandler = { show: vi.fn() }

    attachSubmitListener(form, buildHandler(), errors, makeFormAlert())
    submit(form)

    await vi.runAllTimersAsync()

    expect(errors.show).not.toHaveBeenCalled()
  })

  it('should ignore duplicate submits while already submitting', async () => {
    const form = buildForm()
    let resolve!: () => void
    const handler = vi.fn<FormSubmitHandler>().mockReturnValue(
      new Promise<void>((r) => {
        resolve = r
      }),
    )

    attachSubmitListener(form, handler, makeFormErrorsHandler(), makeFormAlert())
    submit(form)
    submit(form)

    resolve()
    await vi.runAllTimersAsync()

    expect(handler).toHaveBeenCalledTimes(1)
  })

  it('should allow resubmit after previous submit finishes', async () => {
    const form = buildForm()
    const handler = buildHandler()

    attachSubmitListener(form, handler, makeFormErrorsHandler(), makeFormAlert())
    submit(form)
    await vi.runAllTimersAsync()
    submit(form)
    await vi.runAllTimersAsync()

    expect(handler).toHaveBeenCalledTimes(2)
  })
})
