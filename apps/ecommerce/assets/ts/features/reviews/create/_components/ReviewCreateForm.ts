import BaseForm from '@/ts/core/abstracts/BaseForm'

import Alert from '@/ts/shared/elements/alert/_components/Alert'
import FormErrors from '@/ts/shared/elements/form/_components/FormErrors'
import { createSubmitHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import { handleReviewCreateFormSubmit } from '@/ts/features/reviews/create/_handlers/reviewCreateFormHandler'

export default class ReviewCreateForm extends BaseForm {
  constructor(formSelector: string) {
    const alert = new Alert('#reviews-modal .alert', '#reviews-modal .alert__body')
    const errors = new FormErrors(formSelector, '.reviews-modal__body-form-group')
    const handler = createSubmitHandler(handleReviewCreateFormSubmit, alert, errors)

    super(formSelector, alert, errors, handler, false)
  }
}
