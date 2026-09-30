import { mockHttpSubmitFormData } from '@/tests/_support/mocks/core/http.mocks'

mockHttpSubmitFormData()

import { submitFormData } from '@/ts/core/http/_services/submitFormData'

import signupSubmit from '@/ts/features/auth/signup/_services/signupService'

const mockedSubmitFormData = vi.mocked(submitFormData)

describe('signupSubmit()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should call submitFormData with correct URL', async () => {
    mockedSubmitFormData.mockResolvedValueOnce(undefined)
    const formData = new FormData()

    await signupSubmit(formData)

    expect(mockedSubmitFormData).toHaveBeenCalledWith(
      '/signup/store',
      expect.anything(),
      expect.anything(),
      expect.anything(),
    )
  })

  it('should pass formData to submitFormData', async () => {
    mockedSubmitFormData.mockResolvedValueOnce(undefined)
    const formData = new FormData()
    formData.append('email', 'test@example.com')

    await signupSubmit(formData)

    expect(mockedSubmitFormData).toHaveBeenCalledWith(expect.anything(), formData, expect.anything(), expect.anything())
  })

  it('should call submitFormData with withCredentials=true', async () => {
    mockedSubmitFormData.mockResolvedValueOnce(undefined)

    await signupSubmit(new FormData())

    expect(mockedSubmitFormData).toHaveBeenCalledWith(expect.anything(), expect.anything(), true, expect.anything())
  })

  it('should call submitFormData with checkSubmitting=true', async () => {
    mockedSubmitFormData.mockResolvedValueOnce(undefined)

    await signupSubmit(new FormData())

    expect(mockedSubmitFormData).toHaveBeenCalledWith(expect.anything(), expect.anything(), expect.anything(), true)
  })

  it('should return the result from submitFormData', async () => {
    const expected = { message: 'Account created.' }
    mockedSubmitFormData.mockResolvedValueOnce(expected)

    const result = await signupSubmit(new FormData())

    expect(result).toBe(expected)
  })
})
