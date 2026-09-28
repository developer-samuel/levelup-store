import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'
import { loadTurnstile } from '@/ts/shared/loaders/turnstile'

import { handleSignupFormSubmit } from '@/ts/features/auth/signup/_handlers/signupFormHandler'

export default class SignupForm extends BaseForm {
  constructor(formSelector: string) {
    loadTurnstile()

    const alert = new Alert('#signup-page .alert', '#signup-page .alert__body')
    const errors = new FormErrors(formSelector, '.auth-page__card-form-group')
    const handler = createSubmitHandler(handleSignupFormSubmit, alert, errors)

    super(formSelector, alert, errors, handler, true)
  }
}
