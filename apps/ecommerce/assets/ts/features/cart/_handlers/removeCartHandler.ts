import { dispatchLoadingShow, dispatchLoadingHide } from '@/ts/shared/events/loading'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import { handleCartAction } from '@/ts/features/cart/_handlers/cartActionHandler'

let isDeleting = false

export async function handleRemove(event: MouseEvent): Promise<boolean> {
  const removeButton =
    event.target instanceof Element ? event.target.closest<HTMLElement>('.cart__content-destroy') : null
  if (!removeButton) return false

  event.preventDefault()
  if (isDeleting) return true

  isDeleting = true
  dispatchLoadingShow()

  try {
    await handleCartAction(removeButton, 'remove')
  } catch {
    NotyfAlert.error('Something went wrong. Please try again.')
  } finally {
    isDeleting = false
    dispatchLoadingHide()
  }

  return true
}
