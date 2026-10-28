import { queryAll } from '@/ts/shared/utils/dom/query'
import { bindLogoutEvents } from '@/ts/features/auth/logout/_interactions/logoutEvents'

export default class Logout {
  constructor(selector: string) {
    const triggers = queryAll<HTMLElement>(selector)

    bindLogoutEvents(triggers)
  }

  static init(): Logout {
    return new Logout('.logout-btn')
  }
}
