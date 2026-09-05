import { createEventSource } from '@/ts/shared/utils/sse'

const detailEl = document.querySelector<HTMLElement>('.orders__card-detail[data-mercure-hub]')
if (!detailEl) throw new Error('Order detail container not found')

const hubUrl = detailEl.dataset.mercureHub
const orderCode = detailEl.dataset.orderCode

if (!hubUrl || !orderCode) throw new Error('Missing Mercure data attributes')

const eventSource = createEventSource(hubUrl, `orders/${orderCode}/status`)

eventSource.onmessage = (event: MessageEvent<string>): void => {
  const data = JSON.parse(event.data) as {
    orderCode: string
    status: string
  }

  const statusEl = document.querySelector<HTMLElement>('#order-status span:last-child')
  if (!statusEl) return

  statusEl.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1)
}

export {}
