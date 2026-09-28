import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import { handleResetPasswordFormSubmit } from '@/ts/features/auth/password_reset/_handlers/resetPasswordFormHandler'

export default class ResetPasswordForm extends BaseForm {
  constructor(formSelector: string) {
    const alert = new Alert('#reset-password-page .alert', '#reset-password-page .alert__body')
    const errors = new FormErrors(formSelector, '.auth-page__card-form-group')
    const handler = createSubmitHandler(handleResetPasswordFormSubmit, alert, errors)

    super(formSelector, alert, errors, handler, false)
  }
}
