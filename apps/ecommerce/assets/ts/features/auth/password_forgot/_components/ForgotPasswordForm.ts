import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createFormAlertHandler, createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'
import { loadTurnstile } from '@/ts/shared/loaders/turnstile'

import forgotPasswordSubmit from '@/ts/features/auth/password_forgot/_services/forgotPasswordService'

export default class ForgotPasswordForm extends BaseForm {
  constructor(formSelector: string) {
    loadTurnstile()

    const alert = new Alert('#forgot-password-page .alert', '#forgot-password-page .alert__body')
    const errors = new FormErrors(formSelector, '.auth-page__card-form-group')
    const handler = createSubmitHandler(createFormAlertHandler(forgotPasswordSubmit), alert, errors)

    super(formSelector, alert, errors, handler, false)
  }
}
