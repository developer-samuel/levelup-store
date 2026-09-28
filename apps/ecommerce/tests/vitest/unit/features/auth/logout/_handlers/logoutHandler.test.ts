import { mockNotyfAlert } from '@/tests/_support/mocks/plugins/notyf.mocks'

mockNotyfAlert()

vi.mock('@/ts/features/auth/logout/_services/logoutService', () => ({
  logout: vi.fn(),
}))

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import { logout } from '@/ts/features/auth/logout/_services/logoutService'
import { handleLogout } from '@/ts/features/auth/logout/_handlers/logoutHandler'

const mockedLogout = vi.mocked(logout)
const mockedNotyfSuccess = vi.mocked(NotyfAlert.success)

describe('handleLogout()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    Object.defineProperty(window, 'location', { value: { href: '' }, writable: true })
  })

  it('should prevent the default event behavior', async () => {
    mockedLogout.mockResolvedValueOnce(undefined)
    const event = new Event('click')
    const preventDefaultSpy = vi.spyOn(event, 'preventDefault')

    await handleLogout(event)

    expect(preventDefaultSpy).toHaveBeenCalledTimes(1)
  })

  it('should call logout service', async () => {
    mockedLogout.mockResolvedValueOnce(undefined)

    await handleLogout(new Event('click'))

    expect(mockedLogout).toHaveBeenCalledTimes(1)
  })

  it('should show success alert after logout', async () => {
    mockedLogout.mockResolvedValueOnce(undefined)

    await handleLogout(new Event('click'))

    expect(mockedNotyfSuccess).toHaveBeenCalledWith('You have been logged out.')
  })

  it('should redirect to "/" after logout', async () => {
    mockedLogout.mockResolvedValueOnce(undefined)

    await handleLogout(new Event('click'))

    expect(window.location.href).toBe('/')
  })
})
