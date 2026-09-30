import { type KeyboardEvent, type MouseEvent, useEffect, useRef, useState } from 'react'
import { Square } from 'lucide-react'

import { cn } from '@/utils/classes.utils'

import s from '@/chat/input/ChatInput.module.css'

type Props = {
  onSend: (message: string) => void
  onStop: () => void
  loading: boolean
  disabled: boolean
  restoredValue?: string | null
}

const MAX_LENGTH = 2000

export function ChatInput({ onSend, onStop, loading, disabled, restoredValue }: Props) {
  const [value, setValue] = useState('')
  const textareaRef = useRef<HTMLTextAreaElement>(null)

  useEffect(() => {
    if (!restoredValue) return
    setValue(restoredValue)
    textareaRef.current?.focus()
  }, [restoredValue])
  const isOverLimit = value.length > MAX_LENGTH

  const submit = () => {
    const text = value.trim()
    if (!text || disabled || isOverLimit) return
    onSend(text)
    setValue('')
    if (textareaRef.current) {
      textareaRef.current.style.height = 'auto'
    }
  }

  const onKeyDown = (e: KeyboardEvent<HTMLTextAreaElement>) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault()
      submit()
    }
  }

  const onFieldClick = (e: MouseEvent) => {
    if ((e.target as HTMLElement).closest('button')) return
    textareaRef.current?.focus()
  }

  const onInput = () => {
    const el = textareaRef.current
    if (!el) return
    el.style.height = 'auto'
    el.style.height = `${Math.min(el.scrollHeight, 160)}px`
  }

  return (
    <div
      className={s.wrapper}
      onMouseDown={(e) => {
        if (e.target !== textareaRef.current) e.preventDefault()
      }}
    >
      <div className={cn(s.field, isOverLimit && s.fieldError, disabled && s.fieldDisabled)} onClick={onFieldClick}>
        <textarea
          ref={textareaRef}
          id="chat-input"
          rows={1}
          value={value}
          onChange={(e) => setValue(e.target.value)}
          onKeyDown={onKeyDown}
          onInput={onInput}
          placeholder="Ask me anything…"
          disabled={disabled}
          className={cn(s.textarea, disabled && 'cursor-default')}
        />
        <div className={s.footer}>
          <span className={cn(s.counter, isOverLimit && s.counterError)}>
            {value.length}/{MAX_LENGTH}
          </span>
          {loading ? (
            <button className={s.stopBtn} onClick={onStop} aria-label="Stop generating" title="Stop generating">
              <Square className={s.stopBtnIcon} />
            </button>
          ) : (
            <button
              onClick={submit}
              disabled={disabled || !value.trim() || isOverLimit}
              className={cn(s.sendBtn, !value.trim() && s.sendBtnHidden)}
            >
              Send
            </button>
          )}
        </div>
      </div>
      <p className={s.hint}>Enter to send · Shift+Enter for new line</p>
    </div>
  )
}
