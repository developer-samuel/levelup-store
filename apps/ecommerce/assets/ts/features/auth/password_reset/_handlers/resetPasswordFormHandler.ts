import { createFormHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import resetPasswordSubmit from '@/ts/features/auth/password_reset/_services/resetPasswordService'

export const handleResetPasswordFormSubmit = createFormHandler(resetPasswordSubmit, {
  onSuccess() {
    NotyfAlert.success('The password has been changed successfully. You can now log in.')
  },
  defaultRedirect: '/login',
  redirectDelay: 2000,
})
