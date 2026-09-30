import { queryAll } from '@/ts/shared/utils/dom/query'
import { bindLogoutEvents } from '@/ts/features/auth/logout/_interactions/logoutEvents'

export default class Logout {
  constructor(triggerClass: string) {
    const triggers = queryAll<HTMLElement>(`.${triggerClass}`)

    bindLogoutEvents(triggers)
  }
}
