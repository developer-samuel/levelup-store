import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createFormAlertHandler, createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import eanUpdateSubmit from '@/ts/features/admin/products_variants_eans/update/_services/eanUpdateService'

export default class AdminEanUpdateForm extends BaseForm {
  constructor(formSelector: string) {
    const alert = new Alert('#admin-variant-eans-update-page .alert', '#admin-variant-eans-update-page .alert__body')
    const errors = new FormErrors(formSelector, '.admin-detail__data-form-group')
    const handler = createSubmitHandler(createFormAlertHandler(eanUpdateSubmit), alert, errors)

    super(formSelector, alert, errors, handler, true)
  }
}
