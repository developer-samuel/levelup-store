import type { CSSProperties, ReactNode } from 'react'

import { renderOrderStatusBadge } from '@/chat/message/renderer/status/OrderStatus'
import { renderRatingLine } from '@/chat/message/renderer/rating/RatingStar'
import { renderStockLevel } from '@/chat/message/renderer/stock/StockLevel'
import s from '@/chat/message/renderer/InlineParse.module.css'

const COLOR_MAP: Record<string, string> = {
  'text-amber-400': '#fbbf24',
  'text-red-500': '#ef4444',
  'text-green-500': '#22c55e',
  'text-blue-500': '#3b82f6',
}

const PRICE_RE = /^([\d.]+)\s+EUR$/

function classToStyle(cls: string): CSSProperties {
  const style: CSSProperties = {}

  for (const [name, value] of Object.entries(COLOR_MAP)) {
    if (cls.includes(name)) {
      style.color = value
      break
    }
  }

  return style
}

export function inlineParse(text: string, onImageClick?: (url: string) => void): ReactNode[] {
  const ratingNode = renderRatingLine(text)
  if (ratingNode)
    return [
      <span key="r" style={{ display: 'inline-flex', verticalAlign: 'middle' }}>
        {ratingNode}
      </span>,
    ]

  const parts: ReactNode[] = []
  const re =
    /(<span class="([^"]+)">(.+?)<\/span>|\*\*(.+?)\*\*|\*(.+?)\*|~~(.+?)~~|!\[([^\]]*)\]\(((?:[^)(]|\([^)]*\))+)\)|\[([^\]]+)\]\(((?:[^)(]|\([^)]*\))+)\)|Status:\s*(pending|confirmed|processed|shipped|delivered|completed|refunded)|Stock:\s*(\d+))/gi

  let last = 0
  let match: RegExpExecArray | null
  let pendingOriginalPrice: number | null = null

  while ((match = re.exec(text)) !== null) {
    if (match.index > last) {
      const segment = text.slice(last, match.index)

      if (pendingOriginalPrice !== null) {
        const priceMatch = PRICE_RE.exec(segment.trim())

        if (priceMatch) {
          const salePrice = parseFloat(priceMatch[1]!)
          const pct = Math.round((1 - salePrice / pendingOriginalPrice) * 100)

          parts.push(segment)

          if (pct > 0) {
            parts.push(
              <span
                key={`badge${match.index}`}
                className="bg-red-100 text-red-600 text-xs px-1.5 py-0.5 rounded font-semibold ml-1"
              >
                −{pct}%
              </span>,
            )
          }
          pendingOriginalPrice = null
        } else {
          parts.push(segment)
          pendingOriginalPrice = null
        }
      } else {
        parts.push(segment)
      }
    }

    if (match[3]) {
      parts.push(
        <span key={match.index} style={classToStyle(match[2]!)}>
          {match[3]}
        </span>,
      )
    } else if (match[4]) {
      parts.push(<strong key={match.index}>{match[4]}</strong>)
    } else if (match[5]) {
      parts.push(<em key={match.index}>{match[5]}</em>)
    } else if (match[6]) {
      const strikeContent = match[6]!
      const priceMatch = PRICE_RE.exec(strikeContent.trim())

      if (priceMatch) pendingOriginalPrice = parseFloat(priceMatch[1]!)

      parts.push(
        <s key={match.index} className={s.strikeThrough}>
          {strikeContent}
        </s>,
      )
    } else if (match[8]) {
      const imgUrl = match[8]

      parts.push(
        <img
          key={match.index}
          src={imgUrl}
          className="mt-1.5 mb-1.5 h-auto w-auto max-h-16 max-w-full cursor-pointer rounded object-cover"
          onClick={() => onImageClick?.(imgUrl)}
          onError={(e) => {
            ;(e.currentTarget as HTMLImageElement).style.display = 'none'
          }}
        />,
      )
    } else if (match[10]) {
      parts.push(
        <a
          key={match.index}
          href={match[10]}
          target="_blank"
          rel="noopener noreferrer"
          className="font-semibold text-secondary underline"
        >
          {match[9]}
        </a>,
      )
    } else if (match[11]) {
      const badge = renderOrderStatusBadge(match[0])

      parts.push(badge ?? match[0])
    } else if (match[12] !== undefined) {
      const badge = renderStockLevel(match[0])

      parts.push(badge ?? match[0])
    }

    last = match.index + match[0].length
  }

  if (last < text.length) parts.push(text.slice(last))

  return parts
}
