import { mockUtilsScroll, mockUtilsDomQuery } from '@/tests/_support/mocks/shared/utils.mocks'

mockUtilsScroll()
mockUtilsDomQuery()

import { makeFormAlert, makeFormErrorsHandler } from '@/tests/_support/fakers/form.fakers'

import type { AxiosError } from 'axios'

import type { FormResponse } from '@/ts/shared/elements/form/types'
import { scrollToContainer } from '@/ts/shared/utils/scroll'
import { query } from '@/ts/shared/utils/dom/query'
import { handleHttpError } from '@/ts/shared/elements/form/_handlers/httpErrorHandler'

const mockedQuery = vi.mocked(query)
const mockedScrollToContainer = vi.mocked(scrollToContainer)

function makeError(status: number, data: FormResponse): AxiosError<FormResponse> {
  return {
    response: { status, data },
  } as unknown as AxiosError<FormResponse>
}

function makeErrorNoResponse(): AxiosError<FormResponse> {
  return {} as unknown as AxiosError<FormResponse>
}

describe('handleHttpError()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should do nothing when error has no response', () => {
    const alert = makeFormAlert()
    const errors = makeFormErrorsHandler()

    handleHttpError(makeErrorNoResponse(), { alert, errors })

    expect(alert.display).not.toHaveBeenCalled()
    expect(errors.show).not.toHaveBeenCalled()
  })

  it('should call errors.show on status 422 with validation errors', () => {
    const alert = makeFormAlert()
    const errors = makeFormErrorsHandler()
    const validationErrors = { email: ['Required.'] }

    handleHttpError(makeError(422, { errors: validationErrors }), { alert, errors })

    expect(errors.show).toHaveBeenCalledWith(validationErrors)
  })

  it('should scroll to first error field on status 422 by default', () => {
    const el = document.createElement('input')
    el.name = 'email'
    mockedQuery.mockReturnValueOnce(el)

    el.scrollIntoView = vi.fn()
    const scrollSpy = vi.spyOn(el, 'scrollIntoView')
    const focusSpy = vi.spyOn(el, 'focus').mockImplementation(() => {})

    handleHttpError(makeError(422, { errors: { email: ['Required.'] } }), {
      alert: makeFormAlert(),
      errors: makeFormErrorsHandler(),
    })

    expect(scrollSpy).toHaveBeenCalledWith({ behavior: 'smooth', block: 'center' })
    expect(focusSpy).toHaveBeenCalledTimes(1)
  })

  it('should not scroll to error field when shouldScroll=false', () => {
    const el = document.createElement('input')
    mockedQuery.mockReturnValueOnce(el)
    el.scrollIntoView = vi.fn()
    const scrollSpy = vi.spyOn(el, 'scrollIntoView')

    handleHttpError(
      makeError(422, { errors: { email: ['Required.'] } }),
      { alert: makeFormAlert(), errors: makeFormErrorsHandler() },
      false,
    )

    expect(scrollSpy).not.toHaveBeenCalled()
  })

  it('should not call errors.show when status 422 but errors handler is missing', () => {
    const alert = makeFormAlert()

    handleHttpError(makeError(422, { errors: { email: ['Required.'] } }), { alert })

    expect(alert.display).toHaveBeenCalled()
  })

  it('should call alert.display with server message on non-422 status', () => {
    const alert = makeFormAlert()

    handleHttpError(makeError(500, { message: 'Server error.' }), { alert })

    expect(alert.display).toHaveBeenCalledWith(false, 'Server error.')
  })

  it('should call alert.display with fallback message when no message in response', () => {
    const alert = makeFormAlert()

    handleHttpError(makeError(500, {}), { alert })

    expect(alert.display).toHaveBeenCalledWith(false, 'An error occurred.')
  })

  it('should call scrollToTop on non-422 status by default', () => {
    handleHttpError(makeError(422, { message: 'Unprocessable.' }), { alert: makeFormAlert() })

    expect(mockedScrollToContainer).toHaveBeenCalledTimes(1)
  })

  it('should not call scrollToTop on non-422 when shouldScroll=false', () => {
    handleHttpError(makeError(422, { message: 'Unprocessable.' }), { alert: makeFormAlert() }, false)

    expect(mockedScrollToContainer).not.toHaveBeenCalled()
  })

  it('should not scroll when errors object is empty', () => {
    const el = document.createElement('input')
    mockedQuery.mockReturnValueOnce(el)
    el.scrollIntoView = vi.fn()
    const scrollSpy = vi.spyOn(el, 'scrollIntoView')

    handleHttpError(makeError(422, { errors: {} }), {
      alert: makeFormAlert(),
      errors: makeFormErrorsHandler(),
    })

    expect(scrollSpy).not.toHaveBeenCalled()
  })

  it('should not scroll when error field element is not found in DOM', () => {
    mockedQuery.mockReturnValueOnce(null)
    vi.spyOn(document, 'getElementById').mockReturnValueOnce(null)

    expect(() =>
      handleHttpError(makeError(422, { errors: { email: ['Required.'] } }), {
        alert: makeFormAlert(),
        errors: makeFormErrorsHandler(),
      }),
    ).not.toThrow()
  })
})
