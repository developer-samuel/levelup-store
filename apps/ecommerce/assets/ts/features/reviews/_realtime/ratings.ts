import { createEventSource } from '@/ts/shared/utils/sse'

const container = document.querySelector<HTMLElement>('.reviews[data-variant-id]')
if (!container) throw new Error('Reviews container not found')

const variantId = container.dataset.variantId
const hubUrl = container.dataset.mercureHub

if (!variantId || !hubUrl) throw new Error('Missing Mercure data attributes')

const eventSource = createEventSource(hubUrl, `reviews/${variantId}/ratings`)

eventSource.onmessage = (event: MessageEvent<string>): void => {
  const data = JSON.parse(event.data) as {
    reviewId: number
    likesCount: number
    dislikesCount: number
  }

  const ratingEl = container.querySelector<HTMLElement>(
    `.reviews__card-item-row-right[data-mercure-review-id="${data.reviewId}"]`,
  )
  if (!ratingEl) return

  const counts = ratingEl.querySelectorAll<HTMLElement>('.reviews__card-item-row-right-rating-count')

  if (counts[0]) counts[0].textContent = String(data.likesCount)
  if (counts[1]) counts[1].textContent = String(data.dislikesCount)
}

export {}
