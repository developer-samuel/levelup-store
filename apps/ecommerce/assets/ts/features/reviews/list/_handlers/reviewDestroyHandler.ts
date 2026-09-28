import { handleDestroy } from '@/ts/shared/elements/actions/_handlers/destroyHandler'

import { reviewDestroy } from '@/ts/features/reviews/list/_services/reviewDestroyService'

export const handleDeleteClick = handleDestroy({
  datasetKey: 'reviewId',
  serviceFn: reviewDestroy,
  onSuccess: () => {},
  logTag: '[Reviews]',
  successMessage: 'The review was successfully deleted.',
  reloadDelay: 2000,
})
