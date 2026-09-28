vi.mock('@/ts/features/auth/logout/_handlers/logoutHandler', () => ({
  handleLogout: vi.fn(),
}))

import { handleLogout } from '@/ts/features/auth/logout/_handlers/logoutHandler'
import { attachLogoutListener } from '@/ts/features/auth/logout/_listeners/logoutListener'

const mockedHandleLogout = vi.mocked(handleLogout)

describe('attachLogoutListener()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should call handleLogout when the element is clicked', async () => {
    mockedHandleLogout.mockResolvedValueOnce(undefined)
    const trigger = document.createElement('button')

    attachLogoutListener(trigger)
    trigger.click()

    await vi.waitFor(() => {
      expect(mockedHandleLogout).toHaveBeenCalledTimes(1)
    })
  })

  it('should pass the click event to handleLogout', async () => {
    mockedHandleLogout.mockResolvedValueOnce(undefined)
    const trigger = document.createElement('button')

    attachLogoutListener(trigger)
    trigger.click()

    await vi.waitFor(() => {
      const event = mockedHandleLogout.mock.calls[0]?.[0]
      expect(event).toBeInstanceOf(Event)
    })
  })
})
