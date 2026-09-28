import { query } from '@/ts/shared/utils/dom/query'

import { handleDestroyProfile } from '@/ts/features/users/profile_destroy/_handlers/destroyProfileHandler'

export function attachDestroyProfileListener(selector: string): void {
  const button = query<HTMLButtonElement>(selector)
  if (!button) return

  let isDeleting = false

  button.addEventListener('click', () => {
    if (isDeleting) return
    isDeleting = true

    void handleDestroyProfile().finally(() => {
      isDeleting = false
    })
  })
}
