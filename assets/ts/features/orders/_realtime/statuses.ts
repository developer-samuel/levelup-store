import { createEventSource } from '@/ts/shared/utils/sse'

const container = document.querySelector<HTMLElement>('.orders[data-mercure-hub]')
if (!container) throw new Error('Orders container not found')

const hubUrl = container.dataset.mercureHub
if (!hubUrl) throw new Error('Missing Mercure hub URL')

const cards = container.querySelectorAll<HTMLElement>('[data-order-code]')

cards.forEach((card) => {
  const orderCode = card.dataset.orderCode
  if (!orderCode) return

  const eventSource = createEventSource(hubUrl, `orders/${orderCode}/status`)

  eventSource.onmessage = (event: MessageEvent<string>): void => {
    const data = JSON.parse(event.data) as {
      orderCode: string
      status: string
    }

    const statusEl = card.querySelector<HTMLElement>('.orders__card-status')
    if (!statusEl) return

    const capitalized = data.status.charAt(0).toUpperCase() + data.status.slice(1)

    statusEl.textContent = capitalized
    card.className = card.className.replace(/orders__card-box--\S+/, `orders__card-box--${data.status}`)
  }
})

export {}
