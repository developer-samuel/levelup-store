import { mockAxios } from '@/tests/_support/mocks/_external/axios.mocks'

mockAxios()

import axios from 'axios'

import { accessToken } from '@/ts/core/jwt/accessToken'
import { refreshToken } from '@/ts/core/jwt/_services/tokenRefreshService'

const mockedPost = vi.mocked(axios.post)

describe('refreshToken()', () => {
  beforeEach(() => {
    accessToken.clear()
    vi.clearAllMocks()
  })

  it('should store the access token on successful refresh', async () => {
    mockedPost.mockResolvedValueOnce({ data: { access_token: 'new-token-abc' } })

    await refreshToken()

    expect(accessToken.get()).toBe('new-token-abc')
  })

  it('should POST to /api/auth/refresh with credentials', async () => {
    mockedPost.mockResolvedValueOnce({ data: { access_token: 'token' } })

    await refreshToken()

    expect(mockedPost).toHaveBeenCalledWith('/api/auth/refresh', null, { withCredentials: true })
  })

  it('should make only one POST when called concurrently', async () => {
    let resolvePost!: (value: unknown) => void
    const pendingPost = new Promise((resolve) => {
      resolvePost = resolve
    })

    mockedPost.mockReturnValueOnce(pendingPost)

    const first = refreshToken()
    const second = refreshToken()
    const third = refreshToken()

    resolvePost({ data: { access_token: 'concurrent-token' } })

    await Promise.all([first, second, third])

    expect(mockedPost).toHaveBeenCalledTimes(1)
  })

  it('should allow a new refresh after the previous one completes', async () => {
    mockedPost
      .mockResolvedValueOnce({ data: { access_token: 'first-token' } })
      .mockResolvedValueOnce({ data: { access_token: 'second-token' } })

    await refreshToken()
    await refreshToken()

    expect(mockedPost).toHaveBeenCalledTimes(2)
    expect(accessToken.get()).toBe('second-token')
  })

  it('should clear the queue and reset state on error', async () => {
    mockedPost.mockRejectedValueOnce(new Error('Network error'))

    await refreshToken()

    expect(accessToken.get()).toBeNull()

    mockedPost.mockResolvedValueOnce({ data: { access_token: 'recovery-token' } })
    await refreshToken()

    expect(accessToken.get()).toBe('recovery-token')
    expect(mockedPost).toHaveBeenCalledTimes(2)
  })

  it('should clear the queue on error when no token in response', async () => {
    mockedPost.mockResolvedValueOnce({ data: {} })

    await refreshToken()

    expect(accessToken.get()).toBeNull()
  })

  it('should clear the queue on error when response data is null', async () => {
    mockedPost.mockResolvedValueOnce({ data: null })

    await refreshToken()

    expect(accessToken.get()).toBeNull()
  })

  it('should not POST when already refreshing', async () => {
    let resolvePost!: (value: unknown) => void
    const pendingPost = new Promise((resolve) => {
      resolvePost = resolve
    })

    mockedPost.mockReturnValueOnce(pendingPost)

    const first = refreshToken()
    void refreshToken()

    resolvePost({ data: { access_token: 'token' } })
    await first

    expect(mockedPost).toHaveBeenCalledTimes(1)
  })

  it('should process queued callbacks after successful refresh', async () => {
    let resolvePost!: (value: unknown) => void
    const pendingPost = new Promise((resolve) => {
      resolvePost = resolve
    })

    mockedPost.mockReturnValueOnce(pendingPost)

    const results: string[] = []
    const first = refreshToken().then(() => results.push('first'))
    const second = refreshToken().then(() => results.push('second'))

    resolvePost({ data: { access_token: 'queued-token' } })

    await Promise.all([first, second])

    expect(results).toContain('first')
    expect(results).toContain('second')
    expect(accessToken.get()).toBe('queued-token')
  })
})
