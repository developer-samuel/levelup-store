import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import { handleChangePasswordFormSubmit } from '@/ts/features/users/password_change/_handlers/changePasswordFormHandler'

export default class ChangePasswordForm extends BaseForm {
  constructor(formSelector: string) {
    const alert = new Alert('#change-password-page .alert', '#change-password-page .alert__body')
    const errors = new FormErrors(formSelector, '.auth-page__card-form-group')
    const handler = createSubmitHandler(handleChangePasswordFormSubmit, alert, errors)

    super(formSelector, alert, errors, handler, false)
  }
}
