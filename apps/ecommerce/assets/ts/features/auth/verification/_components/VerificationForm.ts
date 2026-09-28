import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createFormAlertHandler, createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import verificationSubmit from '@/ts/features/auth/verification/_services/verificationService'

export default class VerificationForm extends BaseForm {
  constructor(formSelector: string) {
    const alert = new Alert('#verification-page .alert', '#verification-page .alert__body')
    const errors = new FormErrors(formSelector, '.auth-page__card-form-group')
    const handler = createSubmitHandler(createFormAlertHandler(verificationSubmit), alert, errors)

    super(formSelector, alert, errors, handler, false)
  }
}
