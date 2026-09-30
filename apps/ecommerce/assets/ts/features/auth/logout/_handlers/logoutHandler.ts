import { sleep } from '@/ts/shared/utils/sleep'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import { logout } from '@/ts/features/auth/logout/_services/logoutService'

export async function handleLogout(event: Event): Promise<void> {
  event.preventDefault()

  await logout()

  NotyfAlert.success('You have been logged out.')

  await sleep(2000)

  window.location.href = '/'
}
