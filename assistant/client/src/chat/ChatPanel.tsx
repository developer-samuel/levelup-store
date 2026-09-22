import { MessageSquare, RotateCcw, X } from 'lucide-react'

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
  const { messages, loading, error, send, reset } = useChat()

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
            <p className={s.headerTitle}>LevelUp Assistant</p>
            <p className={s.headerSubtitle}>Ask about products</p>
          </div>
          <div className={s.headerActions}>
            {messages.length > 0 && (
              <button className={s.actionBtn} onClick={reset} title="New chat">
                <RotateCcw className={s.headerIconSvg} />
              </button>
            )}
            <button className={s.closeBtn} onClick={onClose} aria-label="Close chat">
              <X className={s.headerIconSvg} />
            </button>
          </div>
        </div>

        <div className={s.divider} />

        <MessageList messages={messages} />

        {error && <div className={s.error}>{error}</div>}

        <ChatInput onSend={send} disabled={loading} />
      </div>
    </>
  )
}
