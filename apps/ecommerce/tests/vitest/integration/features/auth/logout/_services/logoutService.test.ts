import { mockHttpApi } from '@/tests/_support/mocks/core/http.mocks'

mockHttpApi()

import api from '@/ts/core/http/api'
import { accessToken } from '@/ts/core/jwt/accessToken'

import { logout } from '@/ts/features/auth/logout/_services/logoutService'

const mockedPost = vi.mocked(api.post)

describe('logout()', () => {
  beforeEach(() => {
    accessToken.set('existing-token')
    vi.clearAllMocks()
  })

  it('should clear the access token on successful logout', async () => {
    mockedPost.mockResolvedValueOnce({ data: {} })

    await logout()

    expect(accessToken.get()).toBeNull()
  })

  it('should clear the access token even when POST fails', async () => {
    mockedPost.mockRejectedValueOnce(new Error('Network error'))

    await expect(logout()).rejects.toThrow('Network error')

    expect(accessToken.get()).toBeNull()
  })

  it('should POST to /api/auth/logout with credentials', async () => {
    mockedPost.mockResolvedValueOnce({ data: {} })

    await logout()

    expect(mockedPost).toHaveBeenCalledWith(
      '/api/auth/logout',
      null,
      expect.objectContaining({ withCredentials: true }),
    )
  })
})
