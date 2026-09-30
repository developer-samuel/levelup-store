import { mockUtilsScroll } from '@/tests/_support/mocks/shared/utils.mocks'
import { mockNotyfAlert } from '@/tests/_support/mocks/plugins/notyf.mocks'

mockUtilsScroll()
mockNotyfAlert()

vi.mock('@/ts/shared/utils/sleep', () => ({
  sleep: vi.fn().mockResolvedValue(undefined),
}))

vi.mock('@/ts/shared/elements/form/_handlers/httpErrorHandler', () => ({
  handleHttpError: vi.fn(),
}))

import type { StringRecord, StringListRecord } from '@/ts/shared/types'
import type { FormAlert, FormErrorsHandler, FormResponse } from '@/ts/shared/elements/form/types'
import { scrollToContainer } from '@/ts/shared/utils/scroll'
import { sleep } from '@/ts/shared/utils/sleep'
import { handleHttpError } from '@/ts/shared/elements/form/_handlers/httpErrorHandler'
import {
  createFormHandler,
  createFormAlertHandler,
  createSubmitHandler,
} from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

const mockedSleep = vi.mocked(sleep)
const mockedNotyfSuccess = vi.mocked(NotyfAlert.success)

const mockedScrollToContainer = vi.mocked(scrollToContainer)
const mockedHandleHttpError = vi.mocked(handleHttpError)

function buildAlert(): FormAlert & { calls: { success: boolean; message: string }[] } {
  const calls: { success: boolean; message: string }[] = []
  return {
    calls,
    display: (success: boolean, message: string): void => {
      calls.push({ success, message })
    },
  }
}

function buildErrors(): FormErrorsHandler & { shown: StringListRecord[]; cleared: number } {
  const shown: StringListRecord[] = []
  let cleared = 0
  return {
    shown,
    get cleared(): number {
      return cleared
    },
    show: (errors: StringListRecord): void => {
      shown.push(errors)
    },
    clear: (): void => {
      cleared++
    },
  }
}

function buildForm(fields: StringRecord = {}): HTMLFormElement {
  const form = document.createElement('form')

  for (const [name, value] of Object.entries(fields)) {
    const input = document.createElement('input')

    input.name = name
    input.value = value

    form.appendChild(input)
  }
  return form
}

describe('createFormHandler()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    Object.defineProperty(window, 'location', {
      value: { href: '' },
      writable: true,
    })
  })

  it('should call onSuccess callback on successful response', async () => {
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({ success: true, message: 'Done.' })
    const onSuccess = vi.fn()
    const handler = createFormHandler(service, { onSuccess })
    const alert = buildAlert()
    const errors = buildErrors()

    await handler(buildForm(), alert, errors)

    expect(onSuccess).toHaveBeenCalledTimes(1)
  })

  it('should display success message via NotyfAlert when no onSuccess callback', async () => {
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({ success: true, message: 'Saved!' })
    const handler = createFormHandler(service)

    await handler(buildForm(), buildAlert(), buildErrors())

    expect(mockedNotyfSuccess).toHaveBeenCalledWith('Saved!')
  })

  it('should redirect to data.redirect on success', async () => {
    const service = vi
      .fn<() => Promise<FormResponse>>()
      .mockResolvedValueOnce({ success: true, redirect: '/dashboard' })
    const handler = createFormHandler(service)

    await handler(buildForm(), buildAlert(), buildErrors())

    expect(window.location.href).toBe('/dashboard')
  })

  it('should redirect to defaultRedirect when data.redirect is "null"', async () => {
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({ success: true, redirect: 'null' })
    const handler = createFormHandler(service, { defaultRedirect: '/home' })

    await handler(buildForm(), buildAlert(), buildErrors())

    expect(window.location.href).toBe('/home')
  })

  it('should not redirect when redirect is "null" and no defaultRedirect', async () => {
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({ success: true, redirect: 'null' })
    const handler = createFormHandler(service)
    window.location.href = ''

    await handler(buildForm(), buildAlert(), buildErrors())

    expect(window.location.href).toBe('')
  })

  it('should call scrollToTop on success when shouldScroll is true', async () => {
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({ success: false })
    const handler = createFormHandler(service, { shouldScroll: true })

    await handler(buildForm(), buildAlert(), buildErrors())

    expect(mockedScrollToContainer).toHaveBeenCalledTimes(1)
  })

  it('should not call scrollToTop when shouldScroll is false', async () => {
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({ success: true })
    const handler = createFormHandler(service, { shouldScroll: false })

    await handler(buildForm(), buildAlert(), buildErrors())

    expect(mockedScrollToContainer).not.toHaveBeenCalled()
  })

  it('should display error message via alert on failed response', async () => {
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({ success: false, message: 'Invalid.' })
    const handler = createFormHandler(service)
    const alert = buildAlert()

    await handler(buildForm(), alert, buildErrors())

    expect(alert.calls[0]).toEqual({ success: false, message: 'Invalid.' })
  })

  it('should show validation errors on failed response', async () => {
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({
      success: false,
      errors: { email: ['Required.'] },
    })
    const handler = createFormHandler(service)
    const errors = buildErrors()

    await handler(buildForm(), buildAlert(), errors)

    expect(errors.shown[0]).toEqual({ email: ['Required.'] })
  })

  it('should call onError callback on failed response', async () => {
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({ success: false, message: 'Fail.' })
    const onError = vi.fn()
    const handler = createFormHandler(service, { onError })

    await handler(buildForm(), buildAlert(), buildErrors())

    expect(onError).toHaveBeenCalledTimes(1)
  })

  it('should do nothing when success is true but message is undefined and no onSuccess', async () => {
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({ success: true })
    const handler = createFormHandler(service)
    const alert = buildAlert()

    await handler(buildForm(), alert, buildErrors())

    expect(alert.calls).toHaveLength(0)
  })

  it('should return early when service returns null', async () => {
    const service = vi.fn<() => Promise<null>>().mockResolvedValueOnce(null)
    const handler = createFormHandler(service)
    const alert = buildAlert()

    await handler(buildForm(), alert, buildErrors())

    expect(alert.calls).toHaveLength(0)
  })

  it('should call handleHttpError on thrown error', async () => {
    const error = new Error('Server error')
    const service = vi.fn<() => Promise<never>>().mockRejectedValueOnce(error)
    const handler = createFormHandler(service)

    await handler(buildForm(), buildAlert(), buildErrors())

    expect(mockedHandleHttpError).toHaveBeenCalledTimes(1)
  })

  it('should call sleep with redirectDelay before redirecting', async () => {
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({ success: true, redirect: '/home' })
    const handler = createFormHandler(service, { redirectDelay: 2000 })

    await handler(buildForm(), buildAlert(), buildErrors())

    expect(mockedSleep).toHaveBeenCalledWith(2000)
    expect(window.location.href).toBe('/home')
  })

  it('should reload the page after reloadDelay when no redirect', async () => {
    const reloadMock = vi.fn()
    Object.defineProperty(window, 'location', {
      value: { href: '', reload: reloadMock },
      writable: true,
    })
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({ success: true })
    const handler = createFormHandler(service, { reloadDelay: 2000 })

    await handler(buildForm(), buildAlert(), buildErrors())

    expect(mockedSleep).toHaveBeenCalledWith(2000)
    expect(reloadMock).toHaveBeenCalledTimes(1)
  })
})

describe('createFormAlertHandler()', () => {
  it('should display empty string via NotyfAlert when message is undefined', async () => {
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({ success: true })
    const handler = createFormAlertHandler(service)

    await handler(buildForm(), buildAlert(), buildErrors())

    expect(mockedNotyfSuccess).toHaveBeenCalledWith('')
  })

  it('should display success message via NotyfAlert on success', async () => {
    const service = vi.fn<() => Promise<FormResponse>>().mockResolvedValueOnce({ success: true, message: 'Done!' })
    const handler = createFormAlertHandler(service)

    await handler(buildForm(), buildAlert(), buildErrors())

    expect(mockedNotyfSuccess).toHaveBeenCalledWith('Done!')
  })
})

describe('createSubmitHandler()', () => {
  it('should call alert.clear before submitting', async () => {
    const innerHandler = vi.fn<() => Promise<void>>().mockResolvedValueOnce(undefined)
    const alert = { ...buildAlert(), clear: vi.fn() }
    const errors = buildErrors()
    const handler = createSubmitHandler(innerHandler, alert, errors)

    await handler(buildForm(), alert, errors)

    expect(alert.clear).toHaveBeenCalledTimes(1)
  })

  it('should hide alert element when no clear method', async () => {
    const innerHandler = vi.fn<() => Promise<void>>().mockResolvedValueOnce(undefined)
    const element = document.createElement('div')
    element.style.display = 'block'
    const alert = { ...buildAlert(), element }
    const errors = buildErrors()
    const handler = createSubmitHandler(innerHandler, alert, errors)

    await handler(buildForm(), alert, errors)

    expect(element.style.display).toBe('none')
  })

  it('should call errors.clear before submitting', async () => {
    const innerHandler = vi.fn<() => Promise<void>>().mockResolvedValueOnce(undefined)
    const alert = { ...buildAlert(), clear: vi.fn() }
    const errors = buildErrors()
    const handler = createSubmitHandler(innerHandler, alert, errors)

    await handler(buildForm(), alert, errors)

    expect(errors.cleared).toBe(1)
  })

  it('should do nothing when alert has no clear method and no element', async () => {
    const innerHandler = vi.fn<() => Promise<void>>().mockResolvedValueOnce(undefined)
    const alert = buildAlert()
    const errors = buildErrors()
    const handler = createSubmitHandler(innerHandler, alert, errors)

    await handler(buildForm(), alert, errors)

    expect(innerHandler).toHaveBeenCalledTimes(1)
  })

  it('should call the inner submit handler', async () => {
    const innerHandler = vi.fn<() => Promise<void>>().mockResolvedValueOnce(undefined)
    const alert = { ...buildAlert(), clear: vi.fn() }
    const errors = buildErrors()
    const handler = createSubmitHandler(innerHandler, alert, errors)

    await handler(buildForm(), alert, errors)

    expect(innerHandler).toHaveBeenCalledTimes(1)
  })
})
