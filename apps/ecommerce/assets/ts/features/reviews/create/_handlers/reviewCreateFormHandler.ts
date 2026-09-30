import { createFormHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import reviewCreateSubmit from '@/ts/features/reviews/create/_services/reviewCreateService'

export const handleReviewCreateFormSubmit = createFormHandler(reviewCreateSubmit, {
  onSuccess: () => {
    NotyfAlert.success('The review was successfully added.')
  },
  reloadDelay: 2000,
})
