vi.mock('@/ts/core/jwt/isAuth', () => ({
  isAuth: (): boolean => mockIsAuth(),
}))

vi.mock('@/ts/core/jwt/_services/tokenRefreshService', () => ({
  refreshToken: (): Promise<void> => Promise.resolve(mockRefreshToken()),
}))

import { refreshTokenGuard } from '@/ts/core/jwt/authGuard'

const mockIsAuth = vi.fn<() => boolean>()
const mockRefreshToken = vi.fn<() => void>()

describe('refreshTokenGuard()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should call refreshToken when isAuth returns true', async () => {
    mockIsAuth.mockReturnValue(true)

    await refreshTokenGuard()

    expect(mockRefreshToken).toHaveBeenCalledTimes(1)
  })

  it('should not call refreshToken when isAuth returns false', async () => {
    mockIsAuth.mockReturnValue(false)

    await refreshTokenGuard()

    expect(mockRefreshToken).not.toHaveBeenCalled()
  })
})
