import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createFormAlertHandler, createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import brandUpdateSubmit from '@/ts/features/admin/brands/update/_services/brandUpdateService'

export default class AdminBrandUpdateForm extends BaseForm {
  constructor(formSelector: string) {
    const alert = new Alert('#admin-brands-update-page .alert', '#admin-brands-update-page .alert__body')
    const errors = new FormErrors(formSelector, '.admin-detail__data-form-group')
    const handler = createSubmitHandler(createFormAlertHandler(brandUpdateSubmit), alert, errors)

    super(formSelector, alert, errors, handler, true)
  }
}
