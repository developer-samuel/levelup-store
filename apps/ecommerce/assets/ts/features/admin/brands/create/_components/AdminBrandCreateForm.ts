import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createFormAlertHandler, createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import brandCreateSubmit from '@/ts/features/admin/brands/create/_services/brandCreateService'

export default class AdminBrandCreateForm extends BaseForm {
  constructor(formSelector: string) {
    const alert = new Alert('#admin-brands-create-page .alert', '#admin-brands-create-page .alert__body')
    const errors = new FormErrors(formSelector, '.admin-detail__data-form-group')
    const handler = createSubmitHandler(createFormAlertHandler(brandCreateSubmit), alert, errors)

    super(formSelector, alert, errors, handler, true)
  }
}
