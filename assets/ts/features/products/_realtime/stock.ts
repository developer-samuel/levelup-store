import { createEventSource } from '@/ts/shared/utils/sse'

const stockEl = document.querySelector<HTMLElement>('.product-detail__details-stocks[data-variant-id]')
if (!stockEl) throw new Error('Stock container not found')

const variantId = stockEl.dataset.variantId
const hubUrl = stockEl.dataset.mercureHub

if (!variantId || !hubUrl) throw new Error('Missing Mercure data attributes')

const eventSource = createEventSource(hubUrl, `products/${variantId}/stock`)

eventSource.onmessage = (event: MessageEvent<string>): void => {
  const data = JSON.parse(event.data) as {
    quantityAvailable: number
    inStock: boolean
  }

  const p = stockEl.querySelector<HTMLElement>('p')
  if (!p) return

  if (!data.inStock || data.quantityAvailable <= 0) {
    p.textContent = 'There is no stock available.'
    p.className = 'text-red'
  } else if (data.quantityAvailable >= 5) {
    p.textContent = 'In stock quantity > 5'
    p.className = 'text-green'
  } else {
    p.textContent = `In stock quantity: ${data.quantityAvailable}`
    p.className = 'text-orange'
  }
}

export {}
