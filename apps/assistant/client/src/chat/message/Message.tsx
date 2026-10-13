import { useRef, useState } from 'react'
import { createPortal } from 'react-dom'
import { Bot, Square, User, Volume2 } from 'lucide-react'

import { cn } from '@/utils/classes.utils'

import type { Message as MessageType } from '@/chat/chat.types'
import type { useSpeech } from '@/chat/_hooks/useSpeech'
import { usePersistSelection } from '@/chat/_hooks/usePersistSelection'
import { useHideOnScroll } from '@/chat/_hooks/useHideOnScroll'
import { MessageRenderer } from '@/chat/message/renderer/MessageRenderer'
import { ImageLightbox } from '@/chat/message/lightbox/ImageLightbox'
import { formatThinkingTime, formatTimestamp } from '@/chat/message/message.utils'
import s from '@/chat/message/Message.module.css'

type Props = {
  message: MessageType
  speech: ReturnType<typeof useSpeech>
}

export function Message({ message, speech }: Props) {
  const isUser = message.role === 'user'
  const { speakingId, speak, stop } = speech
  const speaking = speakingId === message.id
  const wrapperRef = useRef<HTMLDivElement>(null)
  const bubbleRef = useRef<HTMLDivElement>(null)
  const [tooltipPos, setTooltipPos] = useState<{ top: number; left: number } | null>(null)
  const [lightboxSrc, setLightboxSrc] = useState<string | null>(null)
  usePersistSelection(!!message.streaming && !!message.content)

  useHideOnScroll(() => setTooltipPos(null))

  if (!isUser && !message.content && !message.thinking) return null

  const timestamp = message.createdAt ? formatTimestamp(message.createdAt) : null

  const handleMouseEnter = () => {
    if (!timestamp || !bubbleRef.current) return
    
    const rect = bubbleRef.current.getBoundingClientRect()

    setTooltipPos({
      top: rect.top - 45,
      left: isUser ? rect.right - 130 : rect.left,
    })
  }

  const handleMouseLeave = () => setTooltipPos(null)

  return (
    <>
      <div className={s.row}>
        <div className={cn(s.messageBody, isUser && s.messageBodyUser, isUser && s.rowReverse)}>
          <div className={cn(s.avatar, isUser ? s.avatarUser : s.avatarAi)}>
            {isUser ? <User className={s.avatarIcon} /> : <Bot className={s.avatarIcon} />}
          </div>

          <div
            ref={wrapperRef}
            className={cn(s.bubbleWrapper, isUser ? s.bubbleWrapperUser : s.bubbleWrapperAi)}
          >
            {timestamp && tooltipPos && createPortal(
              <div
                className={cn(s.timestamp, isUser && s.timestampUser)}
                style={{ position: 'fixed', top: tooltipPos.top, left: tooltipPos.left }}
              >
                <span className={s.timestampDate}>{timestamp.date}</span>
                <span className={s.timestampTime}>{timestamp.time}</span>
              </div>,
              document.body,
            )}
            <div
              ref={bubbleRef}
              onMouseEnter={handleMouseEnter}
              onMouseLeave={handleMouseLeave}
              className={cn(
                s.bubble,
                isUser ? s.bubbleUser : s.bubbleAi,
                message.thinking && !message.content && s.bubbleThinking,
              )}
            >
              <MessageRenderer
                content={message.content}
                streaming={message.streaming}
                thinking={message.thinking}
                onImageClick={setLightboxSrc}
              />
            </div>
            {message.thinking && !message.content && (
              <span className={s.thinkingSeconds}>
                Thinking{message.thinkingSeconds !== undefined && ` · ${formatThinkingTime(message.thinkingSeconds)}`}
              </span>
            )}
            {message.content && !message.streaming && (
              <button
                className={cn(s.speakBtn, speaking && s.speakBtnActive)}
                onClick={() => speaking ? stop() : speak(message.id, message.content!)}
                title={speaking ? 'Stop' : 'Read aloud'}
              >
                {speaking ? <Square className={s.speakIcon} /> : <Volume2 className={s.speakIcon} />}
              </button>
            )}
          </div>
        </div>
      </div>

      {lightboxSrc && <ImageLightbox src={lightboxSrc} onClose={() => setLightboxSrc(null)} />}
    </>
  )
}
