import { type ReactNode, useRef, useState } from 'react'
import { createPortal } from 'react-dom'
import { Bot, User } from 'lucide-react'

import { cn } from '@/utils/classes.utils'

import type { Message as MessageType } from '@/chat/chat.types'
import { usePersistSelection } from '@/chat/_hooks/usePersistSelection'
import s from '@/chat/message/Message.module.css'

type Props = {
  message: MessageType
}

// Lightweight markdown renderer - supports bold, bullet lists, numbered lists,
// and paragraph breaks. No external dependencies needed.
function renderMarkdown(text: string): ReactNode[] {
  const nodes: ReactNode[] = []
  const lines = text.split('\n')

  let i = 0

  while (i < lines.length) {
    const line = lines[i] ?? ''

    // Bullet list item: starts with "- " or "* "
    if (/^[-*] /.test(line)) {
      const items: string[] = []

      while (i < lines.length) {
        const cur = lines[i] ?? ''
        const next = lines[i + 1] ?? ''
        if (/^[-*] /.test(cur)) {
          items.push(cur.replace(/^[-*] /, ''))
          i++
        } else if (cur.trim() === '' && /^[-*] /.test(next)) {
          i++
        } else {
          break
        }
      }
      nodes.push(
        <ul key={nodes.length} style={{ paddingLeft: '1.25em', margin: '0.25em 0' }}>
          {items.map((item, j) => (
            <li key={j}>{inlineParse(item)}</li>
          ))}
        </ul>,
      )
      continue
    }

    // Numbered list item: starts with "1. ", "2. ", etc.
    if (/^\d+\. /.test(line)) {
      const items: string[] = []
      while (i < lines.length) {
        const cur = lines[i] ?? ''
        const next = lines[i + 1] ?? ''
        if (/^\d+\. /.test(cur)) {
          items.push(cur.replace(/^\d+\. /, ''))
          i++
        } else if (cur.trim() === '' && /^\d+\. /.test(next)) {
          i++
        } else {
          break
        }
      }
      nodes.push(
        <ol key={nodes.length} style={{ paddingLeft: '1.25em', margin: '0.25em 0' }}>
          {items.map((item, j) => (
            <li key={j}>{inlineParse(item)}</li>
          ))}
        </ol>,
      )
      continue
    }

    if (line.trim() === '') {
      nodes.push(<br key={nodes.length} />)
      i++
      continue
    }

    nodes.push(
      <span key={nodes.length} style={{ display: 'block' }}>
        {inlineParse(line)}
      </span>,
    )
    i++
  }

  return nodes
}

// Parse inline markdown: **bold** and *italic*
function inlineParse(text: string): ReactNode[] {
  const parts: ReactNode[] = []
  const re = /(\*\*(.+?)\*\*|\*(.+?)\*)/g

  let last = 0
  let match: RegExpExecArray | null

  while ((match = re.exec(text)) !== null) {
    if (match.index > last) parts.push(text.slice(last, match.index))
    if (match[2]) parts.push(<strong key={match.index}>{match[2]}</strong>)
    else if (match[3]) parts.push(<em key={match.index}>{match[3]}</em>)

    last = match.index + match[0].length
  }

  if (last < text.length) parts.push(text.slice(last))
  return parts
}

// Format a timestamp as a human-readable date and time string
function formatTimestamp(ts: number): { date: string; time: string } {
  const d = new Date(ts)
  const date = d.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  const time = d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false })

  return { date, time }
}

// Format thinking duration as "Xs", "Xm", or "Xm Ys"
function formatThinkingTime(seconds: number): string {
  if (seconds < 60) return `${seconds}s`

  const m = Math.floor(seconds / 60)
  const s = seconds % 60

  return s > 0 ? `${m}m ${s}s` : `${m}m`
}

export function Message({ message }: Props) {
  const isUser = message.role === 'user'
  const wrapperRef = useRef<HTMLDivElement>(null)
  const bubbleRef = useRef<HTMLDivElement>(null)
  const [tooltipPos, setTooltipPos] = useState<{ top: number; left: number } | null>(null)
  usePersistSelection(!!message.streaming && !!message.content)

  if (!isUser && !message.content && !message.thinking) return null

  const ts = message.createdAt ? formatTimestamp(message.createdAt) : null

  function handleMouseEnter() {
    if (!ts || !bubbleRef.current) return
    const rect = bubbleRef.current.getBoundingClientRect()
    setTooltipPos({
      top: rect.top - 45,
      left: isUser ? rect.right - 130 : rect.left,
    })
  }

  function handleMouseLeave() {
    setTooltipPos(null)
  }

  return (
    <div className={s.row}>
      <div className={cn(s.messageBody, isUser && s.messageBodyUser, isUser && s.rowReverse)}>
        <div className={cn(s.avatar, isUser ? s.avatarUser : s.avatarAi)}>
          {isUser ? <User className={s.avatarIcon} /> : <Bot className={s.avatarIcon} />}
        </div>

        <div
          ref={wrapperRef}
          className={cn(s.bubbleWrapper, isUser ? s.bubbleWrapperUser : s.bubbleWrapperAi)}
          onMouseEnter={handleMouseEnter}
          onMouseLeave={handleMouseLeave}
        >
          {ts && tooltipPos && createPortal(
            <div
              className={cn(s.timestamp, isUser && s.timestampUser)}
              style={{ position: 'fixed', top: tooltipPos.top, left: tooltipPos.left }}
            >
              <span className={s.timestampDate}>{ts.date}</span>
              <span className={s.timestampTime}>{ts.time}</span>
            </div>,
            document.body,
          )}
          <div
            ref={bubbleRef}
            className={cn(
              s.bubble,
              isUser ? s.bubbleUser : s.bubbleAi,
              message.thinking && !message.content && s.bubbleThinking,
            )}
          >
            {message.thinking && !message.content ? (
              <span className={s.dots}>
                <span className={s.dot} />
                <span className={s.dot2} />
                <span className={s.dot3} />
              </span>
            ) : (
              <>
                {renderMarkdown(message.content)}
                {message.streaming && message.content && <span className={s.cursor} />}
              </>
            )}
          </div>
          {message.thinking && !message.content && (
            <span className={s.thinkingSeconds}>
              Thinking{message.thinkingSeconds !== undefined && ` · ${formatThinkingTime(message.thinkingSeconds)}`}
            </span>
          )}
        </div>
      </div>
    </div>
  )
}
