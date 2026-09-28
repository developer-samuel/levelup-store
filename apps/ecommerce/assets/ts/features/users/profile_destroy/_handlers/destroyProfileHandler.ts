import { sleep } from '@/ts/shared/utils/sleep'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import { dispatchLoadingShow, dispatchLoadingHide } from '@/ts/shared/events/loading'

import destroyProfileSubmit from '@/ts/features/users/profile_destroy/_services/destroyProfileService'

export async function handleDestroyProfile(): Promise<void> {
  const confirmed = window.confirm('⚠️ Are you sure you want to delete your account?')
  if (!confirmed) return

  dispatchLoadingShow()

  try {
    const data = await destroyProfileSubmit()

    if (!data) {
      dispatchLoadingHide()
      return
    }

    if (data.success) {
      NotyfAlert.success(data.message ?? 'Account deleted successfully.')
      await sleep(1500)
      window.location.href = '/'
    } else {
      NotyfAlert.error(data.message ?? 'Something went wrong. Please try again.')
      dispatchLoadingHide()
    }
  } catch {
    NotyfAlert.error('Something went wrong. Please try again.')
    dispatchLoadingHide()
  }
}
