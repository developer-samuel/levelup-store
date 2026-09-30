import { createFormHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import loginSubmit from '@/ts/features/auth/login/_services/loginService'

export const handleLoginFormSubmit = createFormHandler(loginSubmit, {
  onSuccess: () => {
    NotyfAlert.success('You have been successfully logged in.')
  },
  defaultRedirect: '/',
  redirectDelay: 2000,
})
