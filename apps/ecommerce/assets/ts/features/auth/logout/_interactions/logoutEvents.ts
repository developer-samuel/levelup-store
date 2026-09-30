import type { HtmlElList } from '@/ts/shared/types'

import { attachLogoutListener } from '@/ts/features/auth/logout/_listeners/logoutListener'

export function bindLogoutEvents(triggers: HtmlElList): void {
  triggers.forEach((trigger) => {
    attachLogoutListener(trigger)
  })
}
