import { type KeyboardEvent, type MouseEvent, useRef, useState } from 'react'

import { cn } from '@/utils/classes.utils'

import s from '@/chat/input/ChatInput.module.css'

type Props = {
  onSend: (message: string) => void
  disabled: boolean
}

const MAX_LENGTH = 2000

export function ChatInput({ onSend, disabled }: Props) {
  const [value, setValue] = useState('')
  const textareaRef = useRef<HTMLTextAreaElement>(null)
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
    <div className={s.wrapper}>
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
          <button
            onClick={submit}
            disabled={disabled || !value.trim() || isOverLimit}
            className={cn(s.sendBtn, !value.trim() && s.sendBtnHidden)}
          >
            Send
          </button>
        </div>
      </div>
      <p className={s.hint}>Enter to send · Shift+Enter for new line</p>
    </div>
  )
}
