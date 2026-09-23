import { useRef } from 'react'
import { Loader2, MessageSquare } from 'lucide-react'

import type { Message as MessageType } from '@/chat/chat.types'
import { useMessagePagination } from '@/chat/message/_hooks/useMessagePagination'
import { useScrollToBottom } from '@/chat/message/_hooks/useScrollToBottom'
import { useScrollAnchor } from '@/chat/message/_hooks/useScrollAnchor'
import { Message } from '@/chat/message/Message'
import s from '@/chat/message/MessageList.module.css'

type Props = {
  messages: MessageType[]
}

export function MessageList({ messages }: Props) {
  const containerRef = useRef<HTMLDivElement>(null)
  const { visibleMessages, hasMore, loadMore } = useMessagePagination(messages)
  const bottomRef = useScrollToBottom(messages.length)
  const sentinelRef = useScrollAnchor(containerRef, visibleMessages.length, hasMore, loadMore)

  if (messages.length === 0) {
    return (
      <div className={s.empty}>
        <div className={s.emptyIcon}>
          <MessageSquare className={s.emptyIconSvg} />
        </div>
        <div>
          <p className={s.emptyTitle}>LevelUp Store Assistant</p>
          <p className={s.emptySubtitle}>Ask me about products, prices or availability.</p>
        </div>
      </div>
    )
  }

  return (
    <div className={s.list} ref={containerRef}>
      <div className={s.messages}>
        {hasMore && (
          <div ref={sentinelRef} className={s.loader}>
            <Loader2 className={s.loaderIcon} />
          </div>
        )}
        {visibleMessages.map((message) => (
          <Message key={message.id} message={message} />
        ))}
        <div ref={bottomRef} />
      </div>
    </div>
  )
}
