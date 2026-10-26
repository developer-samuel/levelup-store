import { useState } from 'react'
import { Clock, MessageSquare, RotateCcw, TriangleAlert, X } from 'lucide-react'

import { cn } from '@/utils/classes.utils'

import { useChat } from '@/chat/_hooks/useChat'
import { useCountdown } from '@/chat/_hooks/useCountdown'
import { useModel } from '@/chat/_hooks/useModel'
import type { HealthStatus } from '@/chat/_hooks/useHealthStatus'
import { useScrollTrigger } from '@/chat/_hooks/useScrollTrigger'
import { useEscapeKey } from '@/chat/_hooks/useEscapeKey'
import { ChatInput } from '@/chat/input/ChatInput'
import { MessageList } from '@/chat/message/MessageList'
import { QueueStatus } from '@/chat/status/QueueStatus'
import s from '@/chat/ChatPanel.module.css'

type Props = {
  open: boolean
  onClose: () => void
  conversationId: string
  isAuthenticated: boolean
  sessionLoaded: boolean
  onConversationReset: (newId: string) => void
  healthStatus: HealthStatus
}

function formatRetryAfter(seconds: number): string {
  if (seconds >= 3600) {
    const h = Math.floor(seconds / 3600)
    const m = Math.floor((seconds % 3600) / 60)

    return m ? `${h}h ${m}m` : `${h}h`
  }

  if (seconds >= 60) return `${Math.floor(seconds / 60)}m`

  return `${seconds}s`
}

export function ChatPanel({
  open,
  onClose,
  conversationId,
  isAuthenticated,
  sessionLoaded,
  onConversationReset,
  healthStatus,
}: Props) {
  const [tooltipVisible, setTooltipVisible] = useState(false)
  const [{ model, ollama }, setModel] = useModel()
  const [isFallback, setIsFallback] = useState(false)
  const { messages, loading, queued, queuePosition, error, rateLimitRetryAfter, failedMessage, send, reset, stop } =
    useChat({
      conversationId,
      isAuthenticated,
      sessionLoaded,
      onConversationReset,
      onModelChange: (newModel, fallback) => {
        setModel(newModel)
        setIsFallback(fallback)
      },
    })

  const countdown = useCountdown(rateLimitRetryAfter)

  const dotStatus = rateLimitRetryAfter !== null || healthStatus === 'offline' ? 'offline' : healthStatus

  useEscapeKey(open, onClose)

  const scrollTrigger = useScrollTrigger(error, queued)

  return (
    <>
      <div className={cn(s.backdrop, open ? s.backdropOpen : s.backdropClosed)} onClick={onClose} />

      <div className={cn(s.panel, open ? s.panelOpen : s.panelClosed)}>
        <div className={s.header}>
          <div className={s.headerIcon}>
            <MessageSquare className={s.headerIconSvg} />
          </div>
          <div className={s.headerMeta}>
            <div className={s.headerTitleRow}>
              <p className={s.headerTitle}>LevelUp Store Assistant</p>
              <span className={s.statusDot} data-status={dotStatus} />
            </div>
            <p className={s.headerSubtitle}>How can I help you?</p>
          </div>
          <div className={s.headerActions}>
            <div
              className={s.infoWrapper}
              onMouseEnter={() => setTooltipVisible(true)}
              onMouseLeave={() => setTooltipVisible(false)}
            >
              <TriangleAlert className={cn(s.infoIcon, isFallback && s.infoIconFallback)} />
              <div className={cn(s.tooltip, tooltipVisible && s.tooltipVisible)}>
                <p className={s.tooltipTitle}>{ollama ? 'Lightweight model notice' : 'Assistant info'}</p>
                {model && <p className={s.tooltipModel}>Model: {model}</p>}
                {ollama && (
                  <ul className={s.tooltipList}>
                    <li>Responses may be slower due to hardware constraints</li>
                    <li>Grammar and phrasing may not always be perfect</li>
                    <li>Best results in English - other languages supported</li>
                    <li>Thinking may take up to 5 minutes</li>
                    <li>Queued requests expire after 15 minutes</li>
                  </ul>
                )}
              </div>
            </div>
            {messages.length > 0 && (
              <button className={s.actionBtn} onClick={reset} title="New chat">
                <RotateCcw className={s.headerIconSvg} />
              </button>
            )}
            <button className={s.closeBtn} onClick={onClose} aria-label="Close chat" title="Close chat">
              <X className={s.headerIconSvg} />
            </button>
          </div>
        </div>

        <div className={s.divider} />

        <MessageList messages={messages} scrollTrigger={scrollTrigger} />

        {rateLimitRetryAfter !== null && (
          <div className={s.rateLimitError}>
            <Clock className={s.rateLimitIcon} />
            <div className={s.rateLimitText}>
              <p className={s.rateLimitTitle}>Request limit reached</p>
              <p className={s.rateLimitSubtitle}>
                {countdown !== null && countdown > 0 ? (
                  <>
                    Please try again in <strong>{formatRetryAfter(countdown)}</strong>.
                  </>
                ) : (
                  'You can try again now.'
                )}
              </p>
            </div>
          </div>
        )}
        {error && <div className={s.error}>{error}</div>}

        {queued && <QueueStatus position={queuePosition} />}

        <ChatInput
          onSend={send}
          onStop={stop}
          loading={loading}
          disabled={loading || queued}
          restoredValue={error ? failedMessage : null}
        />
      </div>
    </>
  )
}
