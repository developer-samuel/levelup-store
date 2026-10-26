import { type ReactNode } from 'react'

import { renderStockLevel } from '@/chat/message/renderer/stock/StockLevel'
import s from '@/chat/message/renderer/product/ProductCard.module.css'

type ProductData = {
  name: string
  brand?: string
  category?: string
  price?: string
  salePrice?: string
  rating?: string
  orders?: string
  description?: string
  stock?: string
  url?: string
  image?: string
}

function _parseBlock(lines: string[]): ProductData {
  const data: ProductData = { name: '' }

  for (const line of lines) {
    const kv = /^([^:]+):\s*(.+)$/.exec(line.trim())
    if (!kv) continue

    const [, key, value] = kv

    switch (key!.toLowerCase().trim()) {
      case 'product':
        data.name = value!
        break
      case 'brand':
        data.brand = value!
        break
      case 'category':
        data.category = value!
        break
      case 'original price':
        data.price = value!
        break
      case 'sale price':
        data.salePrice = value!
        break
      case 'rating':
        data.rating = value!
        break
      case 'orders':
        data.orders = value!
        break
      case 'description':
        data.description = value!
        break
      case 'stock':
        data.stock = value!
        break
      case 'url':
        data.url = value!
        break
      case 'image':
        data.image = value!
        break
    }
  }

  return data
}

function _pricePct(original: string, sale: string): number {
  const o = parseFloat(original)
  const d = parseFloat(sale)

  if (!o || !d) return 0

  return Math.round((1 - d / o) * 100)
}

function _starLabel(rating: string): ReactNode {
  const m = /^([\d.]+)★/.exec(rating)

  if (!m) return <span className={s.meta}>{rating}</span>

  const stars = parseFloat(m[1]!)
  const full = Math.round(stars)

  return (
    <span className={s.meta} title={rating}>
      <span className={s.stars}>
        {'★'.repeat(full)}
        {'☆'.repeat(Math.max(0, 5 - full))}
      </span>{' '}
      {rating}
    </span>
  )
}

export function renderProductCard(
  lines: string[],
  startIndex: number,
  onImageClick?: (url: string) => void,
): [ReactNode, number] {
  let i = startIndex
  const blockLines: string[] = []

  while (i < lines.length) {
    const line = lines[i] ?? ''

    if (blockLines.length > 0 && /^Product:\s+/i.test(line)) break
    if (blockLines.length > 0 && (line.trim() === '' || /^<br\s*\/?>$/i.test(line.trim()))) {
      const peek = lines[i + 1] ?? ''
      if (/^Image:\s+/i.test(peek.trim())) {
        i++
        continue
      }
      i++
      break
    }

    blockLines.push(line)

    i++
  }

  const d = _parseBlock(blockLines)
  if (!d.name) return [null, i]

  const pct = d.price && d.salePrice ? _pricePct(d.price, d.salePrice) : 0
  const stockNode = d.stock != null ? renderStockLevel(`Stock: ${d.stock}`) : null

  const node = (
    <div className={s.card}>
      {d.image ? (
        <img
          src={d.image}
          alt={d.name}
          className={s.image}
          onClick={() => onImageClick?.(d.image!)}
          onError={(e) => {
            ;(e.currentTarget as HTMLImageElement).style.display = 'none'
          }}
        />
      ) : (
        <div className={s.imagePlaceholder}>📦</div>
      )}
      <div className={s.body}>
        {d.url ? (
          <a href={d.url} target="_blank" rel="noopener noreferrer" className={s.name}>
            {d.name}
          </a>
        ) : (
          <span className={s.name}>{d.name}</span>
        )}
        {(d.brand || d.category) && <span className={s.meta}>{[d.brand, d.category].filter(Boolean).join(' · ')}</span>}
        {d.description && <p className={s.description}>{d.description}</p>}
        {(d.price || d.salePrice) && (
          <div className={s.priceRow}>
            {d.salePrice ? (
              <>
                <div className={s.priceStack}>
                  <span className={s.originalPrice}>{d.price}</span>
                  <span className={s.price}>{d.salePrice}</span>
                </div>
                {pct > 0 && <span className={s.discountBadge}>−{pct}%</span>}
              </>
            ) : (
              <span className={s.price}>{d.price}</span>
            )}
          </div>
        )}
        {(d.rating || d.orders) && (
          <div className={s.ratingRow}>
            {d.rating && _starLabel(d.rating)}
            {d.orders && <span className={s.meta}>{d.orders} orders</span>}
          </div>
        )}
        {stockNode && <div className={s.stockRow}>{stockNode}</div>}
        {d.url && (
          <a href={d.url} target="_blank" rel="noopener noreferrer" className={s.button}>
            Show product →
          </a>
        )}
      </div>
    </div>
  )

  return [node, i]
}
