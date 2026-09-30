import { createFormHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import signupSubmit from '@/ts/features/auth/signup/_services/signupService'

export const handleSignupFormSubmit = createFormHandler(signupSubmit, {
  onSuccess() {
    NotyfAlert.success("Your account has been successfully created and you're now signed in.")
  },
  defaultRedirect: '/',
  redirectDelay: 2000,
})
