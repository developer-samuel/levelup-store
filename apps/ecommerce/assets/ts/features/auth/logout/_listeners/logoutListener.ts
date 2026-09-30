import { handleLogout } from '@/ts/features/auth/logout/_handlers/logoutHandler'

export function attachLogoutListener(trigger: HTMLElement): void {
  trigger.addEventListener('click', (event: Event) => {
    void handleLogout(event)
  })
}
