import { Bot, User } from 'lucide-react'

import { cn } from '@/utils/classes.utils'

import type { Message as MessageType } from '@/chat/chat.types'
import s from '@/chat/message/Message.module.css'

type Props = {
  message: MessageType
}

export function Message({ message }: Props) {
  const isUser = message.role === 'user'

  return (
    <div className={cn(s.row, isUser && s.rowReverse)}>
      <div className={cn(s.avatar, isUser ? s.avatarUser : s.avatarAi)}>
        {isUser ? <User className={s.avatarIcon} /> : <Bot className={s.avatarIcon} />}
      </div>

      <div className={cn(s.bubble, isUser ? s.bubbleUser : s.bubbleAi)}>
        {message.thinking && !message.content ? (
          <span className={s.thinking}>
            <span className={s.dots}>
              <span className={s.dot}>•</span>
              <span className={s.dot2}>•</span>
              <span className={s.dot3}>•</span>
            </span>
            {!!message.thinkingSeconds && (
              <span className={s.thinkingSeconds}>{message.thinkingSeconds}s</span>
            )}
          </span>
        ) : (
          <>
            {message.content}
            {message.streaming && message.content && <span className={s.cursor} />}
          </>
        )}
      </div>
    </div>
  )
}
