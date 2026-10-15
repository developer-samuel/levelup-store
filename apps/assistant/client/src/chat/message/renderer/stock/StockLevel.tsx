import { type ReactNode } from 'react'

import s from '@/chat/message/renderer/stock/StockLevel.module.css'

const STOCK_RE = /^Stock:\s*(\d+)/i

export function renderStockLevel(text: string): ReactNode | null {
  const m = STOCK_RE.exec(text)
  if (!m) return null

  const n = parseInt(m[1]!, 10)

  if (n === 0) {
    return <span className={`${s.badge} ${s.outOfStock}`}>Out of stock</span>
  }

  if (n < 5) {
    return <span className={`${s.badge} ${s.lowStock}`}>Low stock ({n} left)</span>
  }

  return <span className={`${s.badge} ${s.inStock}`}>In stock</span>
}
