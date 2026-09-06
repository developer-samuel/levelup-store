import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'
import { loadTurnstile } from '@/ts/shared/loaders/turnstile'

import { handleLoginFormSubmit } from '@/ts/features/auth/login/_handlers/loginFormHandler'

export default class LoginForm extends BaseForm {
  constructor(formSelector: string) {
    loadTurnstile()

    const alert = new Alert('#login-page .alert', '#login-page .alert__body')
    const errors = new FormErrors(formSelector, '.auth-page__card-form-group')
    const handler = createSubmitHandler(handleLoginFormSubmit, alert, errors)

    super(formSelector, alert, errors, handler, false)
  }
}
