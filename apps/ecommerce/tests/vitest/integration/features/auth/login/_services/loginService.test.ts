import { mockHttpApi } from '@/tests/_support/mocks/core/http.mocks'
import { mockSharedEventsLoading } from '@/tests/_support/mocks/shared/events.mocks'

mockHttpApi()
mockSharedEventsLoading()

import api from '@/ts/core/http/api'
import { accessToken } from '@/ts/core/jwt/accessToken'

import { dispatchLoadingHide } from '@/ts/shared/events/loading'

import loginSubmit from '@/ts/features/auth/login/_services/loginService'

const mockedPost = vi.mocked(api.post)
const mockedDispatchLoadingHide = vi.mocked(dispatchLoadingHide)

describe('loginSubmit()', () => {
  beforeEach(() => {
    accessToken.clear()
    vi.clearAllMocks()
  })

  it('should store the access token on successful login', async () => {
    mockedPost.mockResolvedValueOnce({
      data: {
        status: 'success',
        message: 'Logged in.',
        data: { access_token: 'jwt-token-123', redirect: '/' },
      },
    })

    const formData = new FormData()
    formData.set('email', 'user@example.com')
    formData.set('password', 'secret')

    await loginSubmit(formData)

    expect(accessToken.get()).toBe('jwt-token-123')
  })

  it('should return success response with redirect on successful login', async () => {
    mockedPost.mockResolvedValueOnce({
      data: {
        status: 'success',
        message: 'Logged in.',
        data: { access_token: 'jwt-token-123', redirect: '/dashboard' },
      },
    })

    const formData = new FormData()
    formData.set('email', 'user@example.com')
    formData.set('password', 'secret')

    const result = await loginSubmit(formData)

    expect(result).toEqual({ success: true, message: 'Logged in.', redirect: '/dashboard' })
  })

  it('should use default redirect "/" when no redirect in response', async () => {
    mockedPost.mockResolvedValueOnce({
      data: {
        status: 'success',
        message: 'Logged in.',
        data: { access_token: 'token' },
      },
    })

    const formData = new FormData()
    formData.set('email', 'user@example.com')
    formData.set('password', 'secret')

    const result = await loginSubmit(formData)

    expect(result?.redirect).toBe('/')
  })

  it('should dispatch loading hide and not store token on failed login', async () => {
    mockedPost.mockResolvedValueOnce({
      data: { status: 'error', message: 'Invalid credentials.' },
    })

    const formData = new FormData()
    formData.set('email', 'user@example.com')
    formData.set('password', 'wrong')

    const result = await loginSubmit(formData)

    expect(accessToken.get()).toBeNull()
    expect(mockedDispatchLoadingHide).toHaveBeenCalledTimes(1)
    expect(result).toEqual({ success: false, message: 'Invalid credentials.' })
  })

  it('should POST to /api/auth/login with email and password', async () => {
    mockedPost.mockResolvedValueOnce({
      data: { status: 'error', message: 'Invalid credentials.' },
    })

    const formData = new FormData()
    formData.set('email', 'user@example.com')
    formData.set('password', 'secret')

    await loginSubmit(formData)

    expect(mockedPost).toHaveBeenCalledWith(
      '/api/auth/login',
      { email: 'user@example.com', password: 'secret', cf_turnstile_response: '' },
      { withCredentials: true, persistLoading: true },
    )
  })
})
