import { attachDestroyProfileListener } from '@/ts/features/users/profile_destroy/_listeners/destroyProfileListener'

export default class DeleteProfile {
  constructor(selector: string) {
    attachDestroyProfileListener(selector)
  }

  static init(): DeleteProfile {
    return new DeleteProfile('#delete-account-btn')
  }
}
