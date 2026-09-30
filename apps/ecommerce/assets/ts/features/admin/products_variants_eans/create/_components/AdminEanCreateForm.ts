import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createFormAlertHandler, createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import eanCreateSubmit from '@/ts/features/admin/products_variants_eans/create/_services/eanCreateService'

export default class AdminEanCreateForm extends BaseForm {
  constructor(formSelector: string) {
    const alert = new Alert('#admin-variant-eans-create-page .alert', '#admin-variant-eans-create-page .alert__body')
    const errors = new FormErrors(formSelector, '.admin-detail__data-form-group')
    const handler = createSubmitHandler(createFormAlertHandler(eanCreateSubmit), alert, errors)

    super(formSelector, alert, errors, handler, true)
  }
}
