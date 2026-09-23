import { MessageSquare, RotateCcw, Square, TriangleAlert, X } from 'lucide-react'

import { cn } from '@/utils/classes.utils'

import { useChat } from '@/chat/useChat'
import { ChatInput } from '@/chat/input/ChatInput'
import { MessageList } from '@/chat/message/MessageList'
import { useEscapeKey } from '@/chat/useEscapeKey'
import s from '@/chat/ChatPanel.module.css'

type Props = {
  open: boolean
  onClose: () => void
}

export function ChatPanel({ open, onClose }: Props) {
  const { messages, loading, error, send, reset, stop } = useChat()

  useEscapeKey(open, onClose)

  return (
    <>
      <div
        className={cn(s.backdrop, open ? s.backdropOpen : s.backdropClosed)}
        onClick={onClose}
      />

      <div className={cn(s.panel, open ? s.panelOpen : s.panelClosed)}>
        <div className={s.header}>
          <div className={s.headerIcon}>
            <MessageSquare className={s.headerIconSvg} />
          </div>
          <div className={s.headerMeta}>
            <p className={s.headerTitle}>LevelUp Store Assistant</p>
            <p className={s.headerSubtitle}>How can I help you?</p>
          </div>
          <div className={s.headerActions}>
            <div className={s.infoWrapper}>
              <div className={s.infoIconWrapper}>
                <TriangleAlert className={s.infoIcon} />
              </div>
              <div className={s.tooltip}>
                <p className={s.tooltipTitle}>Lightweight model notice</p>
                <p className={s.tooltipModel}>Model: mistral:7b</p>
                <ul className={s.tooltipList}>
                  <li>Responses may be slower due to hardware constraints</li>
                  <li>Grammar and phrasing may not always be perfect</li>
                  <li>Best results in English - other languages supported</li>
                </ul>
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

        <MessageList messages={messages} />

        {error && <div className={s.error}>{error}</div>}

        {loading && (
          <div className={s.stopWrapper}>
            <button className={s.stopBtn} onClick={stop} aria-label="Stop generating" title="Stop generating">
              <Square className={s.stopBtnIcon} />
            </button>
          </div>
        )}

        <ChatInput onSend={send} disabled={loading} />
      </div>
    </>
  )
}
