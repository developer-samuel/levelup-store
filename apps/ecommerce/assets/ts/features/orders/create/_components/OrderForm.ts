import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import { handleOrderFormSubmit } from '@/ts/features/orders/create/_handlers/orderFormHandler'

export default class OrderForm extends BaseForm {
  constructor(formSelector: string) {
    const alert = new Alert('#order-page .alert', '#order-page .alert__body')
    const errors = new FormErrors(formSelector, '.order__card-form-group')
    const handler = createSubmitHandler(handleOrderFormSubmit, alert, errors)

    super(formSelector, alert, errors, handler, true)
  }
}
