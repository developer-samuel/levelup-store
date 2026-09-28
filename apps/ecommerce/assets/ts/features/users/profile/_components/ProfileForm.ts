import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import { handleProfileFormSubmit } from '@/ts/features/users/profile/_handlers/profileFormHandler'

export default class ProfileForm extends BaseForm {
  constructor(formSelector: string) {
    const alert = new Alert('#profile-page .alert', '#profile-page .alert__body')
    const errors = new FormErrors(formSelector, '.user__form-group')
    const handler = createSubmitHandler(handleProfileFormSubmit, alert, errors)

    super(formSelector, alert, errors, handler, true)
  }
}
