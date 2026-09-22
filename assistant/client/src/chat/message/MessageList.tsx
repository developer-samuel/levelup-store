import { MessageSquare } from 'lucide-react'

import type { Message as MessageType } from '@/chat/chat.types'
import { useScrollToBottom } from '@/chat/message/useScrollToBottom'
import { Message } from '@/chat/message/Message'
import s from '@/chat/message/MessageList.module.css'

type Props = {
  messages: MessageType[]
}

export function MessageList({ messages }: Props) {
  const bottomRef = useScrollToBottom(messages)

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
    <div className={s.list}>
      <div className={s.messages}>
        {messages.map((message) => (
          <Message key={message.id} message={message} />
        ))}
        <div ref={bottomRef} />
      </div>
    </div>
  )
}
