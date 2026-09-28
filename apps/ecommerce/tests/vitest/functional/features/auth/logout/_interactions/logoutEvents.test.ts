vi.mock('@/ts/features/auth/logout/_listeners/logoutListener', () => ({
  attachLogoutListener: vi.fn(),
}))

import type { HtmlElList } from '@/ts/shared/types'

import { attachLogoutListener } from '@/ts/features/auth/logout/_listeners/logoutListener'
import { bindLogoutEvents } from '@/ts/features/auth/logout/_interactions/logoutEvents'

const mockedAttachLogoutListener = vi.mocked(attachLogoutListener)

describe('bindLogoutEvents()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should call attachLogoutListener for each trigger', () => {
    const btn1 = document.createElement('button')
    const btn2 = document.createElement('button')
    const triggers = [btn1, btn2] as unknown as HtmlElList

    bindLogoutEvents(triggers)

    expect(mockedAttachLogoutListener).toHaveBeenCalledTimes(2)
  })

  it('should pass each trigger element to attachLogoutListener', () => {
    const btn1 = document.createElement('button')
    const btn2 = document.createElement('button')
    const triggers = [btn1, btn2] as unknown as HtmlElList

    bindLogoutEvents(triggers)

    expect(mockedAttachLogoutListener).toHaveBeenNthCalledWith(1, btn1)
    expect(mockedAttachLogoutListener).toHaveBeenNthCalledWith(2, btn2)
  })

  it('should do nothing when triggers list is empty', () => {
    const triggers = [] as unknown as HtmlElList

    bindLogoutEvents(triggers)

    expect(mockedAttachLogoutListener).not.toHaveBeenCalled()
  })
})
