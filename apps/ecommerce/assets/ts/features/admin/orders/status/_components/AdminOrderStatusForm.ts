import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createFormAlertHandler, createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import orderStatusSubmit from '@/ts/features/admin/orders/status/_services/orderStatusService'

export default class AdminOrderStatusForm extends BaseForm {
  constructor(formSelector: string) {
    const alert = new Alert('#admin-order-status-page .alert', '#admin-order-status-page .alert__body')
    const errors = new FormErrors(formSelector, null)
    const handler = createSubmitHandler(createFormAlertHandler(orderStatusSubmit), alert, errors)

    super(formSelector, alert, errors, handler, true)
  }
}
