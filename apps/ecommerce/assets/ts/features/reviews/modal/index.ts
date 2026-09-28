import { query } from '@/ts/shared/utils/dom/query'

import ReviewModal from '@/ts/features/reviews/modal/_components/ReviewModal'

if (query('#reviews-modal')) {
  new ReviewModal()
}
