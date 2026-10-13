import { type ReactNode } from 'react'

import { inlineParse } from '@/chat/message/renderer/InlineParse'
import { renderList, renderOrderedList } from '@/chat/message/renderer/list/RenderList'
import { renderProductCard } from '@/chat/message/renderer/product/ProductCard'
import { renderRatingLine } from '@/chat/message/renderer/rating/RatingStar'
import { renderOrderStatusLine } from '@/chat/message/renderer/status/OrderStatus'
import { renderStockLevel } from '@/chat/message/renderer/stock/StockLevel'
import { renderTable } from '@/chat/message/renderer/table/RenderTable'
import { renderWishlistHeader } from '@/chat/message/renderer/wishlist/WishlistHeart'

import s from '@/chat/message/renderer/RenderMarkdown.module.css'

type OnImageClick = (url: string) => void

const HEADING_CLASSES = [s.heading1, s.heading2, s.heading3]

export function renderMarkdown(text: string, onImageClick?: OnImageClick): ReactNode[] {
  const nodes: ReactNode[] = []
  const lines = text.split('\n')

  let i = 0

  while (i < lines.length) {
    const line = lines[i] ?? ''

    if (/^#{1,3} /.test(line)) {
      const level = (line.match(/^(#{1,3}) /) ?? [])[1]?.length ?? 1
      const content = line.replace(/^#{1,3} /, '')
      nodes.push(
        <span key={nodes.length} className={HEADING_CLASSES[level - 1]}>
          {inlineParse(content, onImageClick)}
        </span>,
      )
      i++
      continue
    }

    if (/^>\| /.test(line)) {
      const items: string[] = []

      while (i < lines.length && /^>\| /.test(lines[i] ?? '')) {
        items.push((lines[i] ?? '').replace(/^>\| /, ''))
        i++
      }

      nodes.push(
        <div key={nodes.length} className={s.blockquoteCenter}>
          {items.map((item, j) => (
            <span key={j} className={s.blockquoteCenterLine}>{inlineParse(item, onImageClick)}</span>
          ))}
        </div>,
      )
      continue
    }

    if (/^> /.test(line)) {
      const items: string[] = []

      while (i < lines.length && /^> /.test(lines[i] ?? '')) {
        items.push((lines[i] ?? '').replace(/^> /, ''))
        i++
      }

      nodes.push(
        <blockquote key={nodes.length} className={s.blockquote}>
          {items.map((item, j) => (
            <span key={j} className={s.blockquoteLine}>{inlineParse(item, onImageClick)}</span>
          ))}
        </blockquote>,
      )
      continue
    }

    if (/^```/.test(line)) {
      const codeLines: string[] = []
      i++

      while (i < lines.length && !/^```/.test(lines[i] ?? '')) {
        codeLines.push(lines[i] ?? '')
        i++
      }
      i++

      nodes.push(
        <pre key={nodes.length} className={s.pre}>
          <code>{codeLines.join('\n')}</code>
        </pre>,
      )

      continue
    }

    if (/^---+$/.test(line.trim())) {
      nodes.push(<hr key={nodes.length} className={s.hr} />)
      i++

      continue
    }

    if (/^[-*] /.test(line)) {
      const [node, next] = renderList(lines, i, onImageClick)

      nodes.push(<span key={nodes.length}>{node}</span>)
      i = next

      continue
    }

    if (/^\d+\. /.test(line)) {
      const [node, next] = renderOrderedList(lines, i, onImageClick)

      nodes.push(<span key={nodes.length}>{node}</span>)
      i = next

      continue
    }

    if (line.trim().startsWith('|') && line.trim().endsWith('|')) {

      const [node, next] = renderTable(lines, i, onImageClick)
      nodes.push(<span key={nodes.length}>{node}</span>)
      i = next

      continue
    }

    if (line.trim() === '') {
      nodes.push(<br key={nodes.length} />)
      i++

      continue
    }

    if (/^Product:\s+/i.test(line)) {
      const [node, next] = renderProductCard(lines, i, onImageClick)

      if (node) { nodes.push(<span key={nodes.length}>{node}</span>); i = next; continue }
    }

    if (line.startsWith('♡')) {
      const text = line.slice(1).trim()

      nodes.push(<span key={nodes.length}>{renderWishlistHeader(text)}</span>)
      i++

      continue
    }

    const ratingNode = renderRatingLine(line)
    if (ratingNode) {
      nodes.push(<span key={nodes.length} className={s.line}>{ratingNode}</span>)
      i++

      continue
    }

    if (/^Status:\s*(pending|confirmed|processed|shipped|delivered|completed|refunded)/i.test(line)) {
      const node = renderOrderStatusLine(line)

      if (node) { nodes.push(<span key={nodes.length}>{node}</span>); i++; continue }
    }

    if (/^(pending|confirmed|processed|shipped|delivered|completed|refunded)$/i.test(line.trim())) {
      const node = renderOrderStatusLine(`Status: ${line.trim()}`)

      if (node) { nodes.push(<span key={nodes.length}>{node}</span>); i++; continue }
    }

    if (/^Stock:\s*\d+/i.test(line)) {
      const node = renderStockLevel(line)

      if (node) { nodes.push(<span key={nodes.length} className={s.line}>{node}</span>); i++; continue }
    }

    nodes.push(
      <span key={nodes.length} className={s.line}>
        {inlineParse(line, onImageClick)}
      </span>,
    )
    i++
  }

  return nodes
}
