import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createFormAlertHandler, createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import descriptionCreateSubmit from '@/ts/features/admin/products_variants_descriptions/create/_services/descriptionCreateService'

export default class AdminDescriptionCreateForm extends BaseForm {
  constructor(formSelector: string) {
    const alert = new Alert(
      '#admin-variant-descriptions-create-page .alert',
      '#admin-variant-descriptions-create-page .alert__body',
    )
    const errors = new FormErrors(formSelector, '.admin-detail__data-form-group')
    const handler = createSubmitHandler(createFormAlertHandler(descriptionCreateSubmit), alert, errors)

    super(formSelector, alert, errors, handler, true)
  }
}
